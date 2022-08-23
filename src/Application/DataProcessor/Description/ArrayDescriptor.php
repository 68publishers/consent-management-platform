<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use Nette\Schema\Expect;
use Nette\Schema\Schema;
use App\Application\DataProcessor\Context\ContextInterface;

final class ArrayDescriptor implements DescriptorInterface
{
	private DescriptorInterface $valueDescriptor;

	private ?DescriptorInterface $keyDescriptor;

	private function __construct()
	{
	}

	/**
	 * @param \App\Application\DataProcessor\Description\DescriptorInterface      $valueDescriptor
	 * @param \App\Application\DataProcessor\Description\DescriptorInterface|NULL $keyDescriptor
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
	public function schema(ContextInterface $context): Schema
	{
		return Expect::arrayOf(
			$this->valueDescriptor->schema($context),
			NULL !== $this->keyDescriptor ? $this->keyDescriptor->schema($context) : NULL
		);
	}
}
