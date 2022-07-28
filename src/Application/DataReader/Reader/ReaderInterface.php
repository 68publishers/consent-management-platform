<?php

declare(strict_types=1);

namespace App\Application\DataReader\Reader;

use App\Application\DataReader\Description\DescriptorInterface;

interface ReaderInterface
{
	/**
	 * @param \App\Application\DataReader\Description\DescriptorInterface|NULL $descriptor
	 * @param callable|NULL                                                    $onError
	 *
	 * @return iterable|\App\Application\DataReader\RowInterface[]
	 */
	public function read(?DescriptorInterface $descriptor = NULL, ?callable $onError = NULL): iterable;
}
