<?php

declare(strict_types=1);

namespace App\Application\DataReader\Reader;

use App\Application\DataReader\Resource\ResourceInterface;

interface ReaderFactoryInterface
{
	/**
	 * @param string                                                 $format
	 * @param \App\Application\DataReader\Resource\ResourceInterface $resource
	 *
	 * @return bool
	 */
	public function accepts(string $format, ResourceInterface $resource): bool;

	/**
	 * @param \App\Application\DataReader\Resource\ResourceInterface $resource
	 *
	 * @return \App\Application\DataReader\Reader\ReaderInterface
	 * @throws \App\Application\DataReader\Exception\ReaderException
	 */
	public function create(ResourceInterface $resource): ReaderInterface;
}
