<?php

declare(strict_types=1);

namespace App\Application\DataReader\Description;

use Nette\Schema\Schema;
use Nette\Schema\Elements\Structure;
use App\Application\DataReader\Context\ContextInterface;

final class StructureDescriptor implements DescriptorInterface
{
	/** @var \App\Application\DataReader\Description\DescriptorInterface[]  */
	private array $descriptors = [];

	/** @var \App\Application\DataReader\Description\StructureDescriptorPropertyInterface[]  */
	private array $properties = [];

	private function __construct()
	{
	}

	/**
	 * @param \App\Application\DataReader\Description\DescriptorInterface[]                $descriptors
	 * @param \App\Application\DataReader\Description\StructureDescriptorPropertyInterface ...$properties
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
	 * @param string                                                      $name
	 * @param \App\Application\DataReader\Description\DescriptorInterface $descriptor
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
	 * @param \App\Application\DataReader\Description\StructureDescriptorPropertyInterface ...$properties
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
	public function getSchema(ContextInterface $context): Schema
	{
		$structure = new Structure(
			array_map(
				static fn (DescriptorInterface $descriptor): Schema => $descriptor->getSchema($context),
				$this->descriptors
			)
		);

		$structure->castTo('array');

		foreach ($this->properties as $property) {
			$structure = $property->applyToStructure($structure, $context);
		}

		return $structure;
	}
}
