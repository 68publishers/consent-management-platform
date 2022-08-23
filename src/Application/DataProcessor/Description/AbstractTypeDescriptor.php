<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use Nette\Schema\Schema;
use Nette\Schema\Elements\Type;
use App\Application\DataProcessor\Description\Path\Path;
use App\Application\DataProcessor\Context\ContextInterface;
use App\Application\DataProcessor\Description\Path\PathInfo;

abstract class AbstractTypeDescriptor implements DescriptorInterface
{
	/** @var \App\Application\DataProcessor\Description\TypeDescriptorPropertyInterface[]  */
	private array $properties = [];

	private function __construct()
	{
	}

	/**
	 * @param \App\Application\DataProcessor\Description\TypeDescriptorPropertyInterface ...$properties
	 *
	 * @return static
	 */
	public static function create(TypeDescriptorPropertyInterface ...$properties): self
	{
		$descriptor = new static();

		if (!empty($properties)) {
			$descriptor = $descriptor->withProps(...$properties);
		}

		return $descriptor;
	}

	/**
	 * {@inheritDoc}
	 */
	public function schema(ContextInterface $context): Schema
	{
		$type = $this->createType($context);

		foreach ($this->properties as $property) {
			$type = $property->applyToType($type, $context);
		}

		return $type;
	}

	/**
	 * {@inheritDoc}
	 */
	public function pathInfo(Path $path): PathInfo
	{
		$part = $path->shift();
		$pathInfo = new PathInfo();

		if (NULL === $part) {
			$pathInfo->descriptor = $this;
			$pathInfo->found = TRUE;
			$pathInfo->isFinal = TRUE;

			return $pathInfo;
		}

		$pathInfo->descriptor = NULL;
		$pathInfo->found = FALSE;
		$pathInfo->isFinal = FALSE;

		return $pathInfo;
	}

	/**
	 * @param \App\Application\DataProcessor\Description\TypeDescriptorPropertyInterface ...$properties
	 *
	 * @return $this
	 */
	public function withProps(TypeDescriptorPropertyInterface ...$properties): self
	{
		$descriptor = clone $this;
		$descriptor->properties = array_merge($this->properties, $properties);

		return $descriptor;
	}

	/**
	 * @param \App\Application\DataProcessor\Context\ContextInterface $context
	 *
	 * @return \Nette\Schema\Elements\Type
	 */
	abstract protected function createType(ContextInterface $context): Type;

	/**
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	protected function tryToConvertWeakNullValue($value)
	{
		if (!is_string($value)) {
			return $value;
		}

		$val = trim($value);

		if ((empty($val) && !$this->isAnyOf([Required::class]))
			|| (('null' === $val || 'NULL' === $val) && $this->isAnyOf([Nullable::class]))
		) {
			$value = NULL;
		}

		return $value;
	}

	/**
	 * @param string[] $propertyClassnames
	 *
	 * @return bool
	 */
	private function isAnyOf(array $propertyClassnames): bool
	{
		return 0 < count(
			array_filter(
				$this->properties,
				static fn (TypeDescriptorPropertyInterface $property): bool => in_array(get_class($property), $propertyClassnames, TRUE)
			)
		);
	}
}
