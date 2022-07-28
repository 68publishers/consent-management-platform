<?php

declare(strict_types=1);

namespace App\Application\DataReader\Description;

use Nette\Schema\Expect;
use Nette\Schema\Schema;
use App\Application\DataReader\Context\ContextInterface;

final class ArrayDescriptor implements DescriptorInterface
{
	private DescriptorInterface $valueDescriptor;

	private ?DescriptorInterface $keyDescriptor;

	private function __construct()
	{
	}

	/**
	 * @param \App\Application\DataReader\Description\DescriptorInterface      $valueDescriptor
	 * @param \App\Application\DataReader\Description\DescriptorInterface|NULL $keyDescriptor
	 *
	 * @return static
	 */
	public static function create(DescriptorInterface $valueDescriptor, ?DescriptorInterface $keyDescriptor = NULL): self
	{
		$descriptor = new self();
		$descriptor->valueDescriptor = $valueDescriptor;
		$descriptor->keyDescriptor = $keyDescriptor;

		return $descriptor;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSchema(ContextInterface $context): Schema
	{
		return Expect::arrayOf(
			$this->valueDescriptor->getSchema($context),
			NULL !== $this->keyDescriptor ? $this->keyDescriptor->getSchema($context) : NULL
		);
	}
}
