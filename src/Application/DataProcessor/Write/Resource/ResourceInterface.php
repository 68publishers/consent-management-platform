<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Resource;

use App\Application\DataProcessor\Description\DescriptorInterface;

interface ResourceInterface
{
	/**
	 * @return iterable|\App\Application\DataProcessor\RowInterface[]
	 */
	public function rows(): iterable;

	/**
	 * @return \App\Application\DataProcessor\Description\DescriptorInterface|null
	 */
	public function descriptor(): ?DescriptorInterface;

	/**
	 * @return string
	 */
	public function __toString(): string;
}
