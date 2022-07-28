<?php

declare(strict_types=1);

namespace App\Application\DataReader;

use App\Application\DataReader\Resource\FileResource;
use App\Application\DataReader\Reader\ReaderInterface;
use App\Application\DataReader\Resource\ArrayResource;
use App\Application\DataReader\Resource\StringResource;
use App\Application\DataReader\Exception\ReaderException;
use App\Application\DataReader\Resource\ResourceInterface;
use App\Application\DataReader\Reader\ReaderFactoryInterface;

final class DataReaderFactory implements DataReaderFactoryInterface
{
	/** @var \App\Application\DataReader\Reader\ReaderFactoryInterface[]  */
	private array $readerFactories;

	/**
	 * @param \App\Application\DataReader\Reader\ReaderFactoryInterface[] $readerFactories
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
