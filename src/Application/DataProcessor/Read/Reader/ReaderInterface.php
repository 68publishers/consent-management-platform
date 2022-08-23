<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Reader;

use App\Application\DataProcessor\Description\DescriptorInterface;
use App\Application\DataProcessor\Write\Resource\ResourceInterface as WritableResource;

interface ReaderInterface
{
	/**
	 * @param \App\Application\DataProcessor\Description\DescriptorInterface|NULL $descriptor
	 * @param callable|NULL                                                       $onError
	 *
	 * @return iterable|\App\Application\DataProcessor\RowInterface[]
	 */
	public function read(?DescriptorInterface $descriptor = NULL, ?callable $onError = NULL): iterable;

	/**
	 * @param \App\Application\DataProcessor\Description\DescriptorInterface|null $descriptor
	 * @param callable|NULL                                                       $onError
	 *
	 * @return \App\Application\DataProcessor\Write\Resource\ResourceInterface
	 */
	public function toWritableResource(?DescriptorInterface $descriptor = NULL, ?callable $onError = NULL): WritableResource;
}
