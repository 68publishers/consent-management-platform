<?php

declare(strict_types=1);

namespace App\Application\DataReader\Reader;

use App\Application\DataReader\Resource\FileResource;
use App\Application\DataReader\Resource\StringResource;
use App\Application\DataReader\Exception\ReaderException;
use App\Application\DataReader\Resource\ResourceInterface;

final class CsvReaderFactory implements ReaderFactoryInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function accepts(string $format, ResourceInterface $resource): bool
	{
		return 'csv' === $format && ($resource instanceof FileResource || $resource instanceof StringResource);
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(ResourceInterface $resource): ReaderInterface
	{
		if ($resource instanceof StringResource) {
			return CsvReader::fromString($resource);
		}

		if ($resource instanceof FileResource) {
			return CsvReader::fromFile($resource);
		}

		throw ReaderException::unacceptableResource('csv', $resource);
	}
}
