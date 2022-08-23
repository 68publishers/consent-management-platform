<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read;

use App\Application\DataProcessor\Exception\ReaderException;
use App\Application\DataProcessor\Read\Resource\FileResource;
use App\Application\DataProcessor\Read\Reader\ReaderInterface;
use App\Application\DataProcessor\Read\Resource\ArrayResource;
use App\Application\DataProcessor\Read\Resource\StringResource;
use App\Application\DataProcessor\Read\Resource\ResourceInterface;
use App\Application\DataProcessor\Read\Reader\ReaderFactoryInterface;

final class DataReaderFactory implements DataReaderFactoryInterface
{
	/** @var \App\Application\DataProcessor\Read\Reader\ReaderFactoryInterface[]  */
	private array $readerFactories;

	/**
	 * @param \App\Application\DataProcessor\Read\Reader\ReaderFactoryInterface[] $readerFactories
	 */
	public function __construct(array $readerFactories)
	{
		$this->readerFactories = (static fn (ReaderFactoryInterface ...$readerFactories): array => $readerFactories)(...$readerFactories);
	}

	/**
	 * {@inheritDoc}
	 */
	public function fromResource(string $format, ResourceInterface $resource): ReaderInterface
	{
		foreach ($this->readerFactories as $readerFactory) {
			if ($readerFactory->accepts($format, $resource)) {
				return $readerFactory->create($resource);
			}
		}

		throw ReaderException::unresolvableResource($format, $resource);
	}

	/**
	 * {@inheritDoc}
	 */
	public function fromFile(string $format, string $filename, array $options = []): ReaderInterface
	{
		return $this->fromResource($format, FileResource::create($filename, $options));
	}

	/**
	 * {@inheritDoc}
	 */
	public function fromString(string $format, string $string, array $options = []): ReaderInterface
	{
		return $this->fromResource($format, StringResource::create($string, $options));
	}

	/**
	 * {@inheritDoc}
	 */
	public function fromArray(array $data, array $options = []): ReaderInterface
	{
		return $this->fromResource('array', ArrayResource::create($data, $options));
	}
}
