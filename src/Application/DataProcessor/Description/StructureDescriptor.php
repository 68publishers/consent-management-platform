<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use Nette\Schema\Schema;
use Nette\Schema\Elements\Structure;
use App\Application\DataProcessor\Description\Path\Path;
use App\Application\DataProcessor\Context\ContextInterface;
use App\Application\DataProcessor\Description\Path\PathInfo;

final class StructureDescriptor implements DescriptorInterface
{
	/** @var \App\Application\DataProcessor\Description\DescriptorInterface[]  */
	private array $descriptors = [];

	/** @var \App\Application\DataProcessor\Description\StructureDescriptorPropertyInterface[]  */
	private array $properties = [];

	private function __construct()
	{
	}

	/**
	 * @param \App\Application\DataProcessor\Description\DescriptorInterface[]                $descriptors
	 * @param \App\Application\DataProcessor\Description\StructureDescriptorPropertyInterface ...$properties
	 *
	 * @return static
	 */
	public static function create(array $descriptors, StructureDescriptorPropertyInterface ...$properties): self
	{
		(static fn (DescriptorInterface ...$descriptors) => NULL)(...array_values($descriptors));

		$structure = new self();
		$structure->descriptors = $descriptors;
		$structure->properties = $properties;

		return $structure;
	}

	/**
	 * @param string                                                         $name
	 * @param \App\Application\DataProcessor\Description\DescriptorInterface $descriptor
	 *
	 * @return $this
	 */
	public function withDescriptor(string $name, DescriptorInterface $descriptor): self
	{
		$structure = clone $this;
		$structure->descriptors[$name] = $descriptor;

		return $structure;
	}

	/**
	 * @param \App\Application\DataProcessor\Description\StructureDescriptorPropertyInterface ...$properties
	 *
	 * @return $this
	 */
	public function withProps(StructureDescriptorPropertyInterface ...$properties): self
	{
		$structure = clone $this;
		$structure->properties = array_merge($this->properties, $properties);

		return $structure;
	}

	/**
	 * {@inheritDoc}
	 */
	public function schema(ContextInterface $context): Schema
	{
		$structure = new Structure(
			array_map(
				static fn (DescriptorInterface $descriptor): Schema => $descriptor->schema($context),
				$this->descriptors
			)
		);

		$structure->castTo('array');

		foreach ($this->properties as $property) {
			$structure = $property->applyToStructure($structure, $context);
		}

		return $structure;
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
			$pathInfo->isFinal = FALSE;

			return $pathInfo;
		}

		if (!isset($this->descriptors[$part])) {
			$pathInfo->descriptor = NULL;
			$pathInfo->found = FALSE;
			$pathInfo->isFinal = FALSE;

			return $pathInfo;
		}

		return $this->descriptors[$part]->pathInfo($path);
	}
}
