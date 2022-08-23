<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write;

use App\Application\DataProcessor\Exception\WriterException;
use App\Application\DataProcessor\Write\Writer\WriterInterface;
use App\Application\DataProcessor\Write\Resource\ResourceInterface;
use App\Application\DataProcessor\Write\Destination\FileDestination;
use App\Application\DataProcessor\Write\Destination\StringDestination;
use App\Application\DataProcessor\Write\Writer\WriterFactoryInterface;
use App\Application\DataProcessor\Write\Destination\DestinationInterface;

final class DataWriterFactory implements DataWriterFactoryInterface
{
	/** @var \App\Application\DataProcessor\Write\Writer\WriterFactoryInterface[]  */
	private array $writerFactories;

	/**
	 * @param \App\Application\DataProcessor\Write\Writer\WriterFactoryInterface[] $writerFactories
	 */
	public function __construct(array $writerFactories)
	{
		$this->writerFactories = (static fn (WriterFactoryInterface ...$writerFactories): array => $writerFactories)(...$writerFactories);
	}

	/**
	 * {@inheritDoc}
	 */
	public function toDestination(string $format, ResourceInterface $resource, DestinationInterface $destination): WriterInterface
	{
		foreach ($this->writerFactories as $readerFactory) {
			if ($readerFactory->accepts($format, $destination)) {
				return $readerFactory->create($resource, $destination);
			}
		}

		throw WriterException::unresolvableDestination($format, $destination);
	}

	/**
	 * {@inheritDoc}
	 */
	public function toFile(string $format, ResourceInterface $resource, string $filename, array $options = []): WriterInterface
	{
		return $this->toDestination($format, $resource, FileDestination::create($filename, $options));
	}

	/**
	 * {@inheritDoc}
	 */
	public function toString(string $format, ResourceInterface $resource, array $options = []): WriterInterface
	{
		return $this->toDestination($format, $resource, StringDestination::create($options));
	}
}
