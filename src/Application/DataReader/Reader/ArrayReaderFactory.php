<?php

declare(strict_types=1);

namespace App\Application\DataReader\Reader;

use App\Application\DataReader\Resource\ArrayResource;
use App\Application\DataReader\Exception\ReaderException;
use App\Application\DataReader\Resource\ResourceInterface;

final class ArrayReaderFactory implements ReaderFactoryInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function accepts(string $format, ResourceInterface $resource): bool
	{
		return 'array' === $format && $resource instanceof ArrayResource;
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(ResourceInterface $resource): ReaderInterface
	{
		if ($resource instanceof ArrayResource) {
			return ArrayReader::fromArray($resource);
		}

		throw ReaderException::unacceptableResource('array', $resource);
	}
}
