<?php

declare(strict_types=1);

namespace App\Application\DataReader\Reader;

use App\Application\DataReader\Resource\FileResource;
use App\Application\DataReader\Exception\ReaderException;
use App\Application\DataReader\Resource\ResourceInterface;

final class PhpReaderFactory implements ReaderFactoryInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function accepts(string $format, ResourceInterface $resource): bool
	{
		return 'php' === $format && $resource instanceof FileResource;
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(ResourceInterface $resource): ReaderInterface
	{
		if ($resource instanceof FileResource) {
			return PhpReader::fromFile($resource);
		}

		throw ReaderException::unacceptableResource('php', $resource);
	}
}
