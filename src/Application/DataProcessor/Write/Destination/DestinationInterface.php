<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Destination;

interface DestinationInterface
{
	/**
	 * @return array
	 */
	public function options(): array;

	/**
	 * @return string
	 */
	public function __toString(): string;
}
