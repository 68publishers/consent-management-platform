<?php

declare(strict_types=1);

namespace App\Application\DataReader\Description;

use Nette\Schema\Elements\Type;
use App\Application\DataReader\Context\ContextInterface;

final class Range implements TypeDescriptorPropertyInterface
{
	private array $range;

	/**
	 * @param float|NULL $min
	 * @param float|NULL $max
	 */
	public function __construct(?float $min, ?float $max)
	{
		$this->range = [$min, $max];
	}

	/**
	 * {@inheritDoc}
	 */
	public function applyToType(Type $type, ContextInterface $context): Type
	{
		return $type
			->min($this->range[0])
			->max($this->range[1]);
	}
}
