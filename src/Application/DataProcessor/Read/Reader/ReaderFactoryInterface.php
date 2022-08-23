<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Reader;

use App\Application\DataProcessor\Read\Resource\ResourceInterface;

interface ReaderFactoryInterface
{
	/**
	 * @param string                                                         $format
	 * @param \App\Application\DataProcessor\Read\Resource\ResourceInterface $resource
	 *
	 * @return bool
	 */
	public function accepts(string $format, ResourceInterface $resource): bool;

	/**
	 * @param \App\Application\DataProcessor\Read\Resource\ResourceInterface $resource
	 *
	 * @return \App\Application\DataProcessor\Read\Reader\ReaderInterface
	 * @throws \App\Application\DataProcessor\Exception\ReaderException
	 */
	public function create(ResourceInterface $resource): ReaderInterface;
}
