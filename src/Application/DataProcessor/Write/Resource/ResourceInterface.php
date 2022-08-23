<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Resource;

interface ResourceInterface
{
	/**
	 * @return iterable|\App\Application\DataProcessor\RowInterface[]
	 */
	public function rows(): iterable;

	/**
	 * @return string
	 */
	public function __toString(): string;
}
