<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Reader;

use App\Application\DataProcessor\Exception\ReaderException;
use App\Application\DataProcessor\Read\Resource\FileResource;
use App\Application\DataProcessor\Read\Resource\ResourceInterface;

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
