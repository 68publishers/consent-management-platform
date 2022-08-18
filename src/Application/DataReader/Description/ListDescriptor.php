<?php

declare(strict_types=1);

namespace App\Application\DataReader\Description;

use Nette\Schema\Expect;
use Nette\Schema\Schema;
use App\Application\DataReader\Context\ContextInterface;

final class ListDescriptor implements DescriptorInterface
{
	private DescriptorInterface $valueDescriptor;

	private function __construct()
	{
	}

	/**
	 * @param \App\Application\DataReader\Description\DescriptorInterface $valueDescriptor
	 *
	 * @return static
	 */
	public static function create(DescriptorInterface $valueDescriptor): self
	{
		$descriptor = new self();
		$descriptor->valueDescriptor = $valueDescriptor;

		return $descriptor;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSchema(ContextInterface $context): Schema
	{
		$list = Expect::listOf(
			$this->valueDescriptor->getSchema($context)
		);

		if (TRUE === ($context[ContextInterface::WEAK_TYPES] ?? FALSE)) {
			$list->before(function ($value) {
				if (!is_scalar($value)) {
					return $value;
				}

				$value = trim((string) $value);

				if ('' === $value) {
					return [];
				}

				$value = explode(',', $value);

				return array_map('trim', $value);
			});
		}

		return $list;
	}
}
