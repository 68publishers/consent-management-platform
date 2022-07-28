<?php

declare(strict_types=1);

namespace App\Application\DataReader\Resource;

interface ResourceInterface
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
