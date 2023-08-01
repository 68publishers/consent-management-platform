<?php

declare(strict_types=1);

namespace App\Application\DataProcessor;

use App\Application\DataProcessor\Description\DescriptorInterface;
use App\Application\DataProcessor\Read\Reader\ReaderInterface;
use App\Application\DataProcessor\Write\DataWriterFactoryInterface;
use App\Application\DataProcessor\Write\Destination\DestinationInterface;
use App\Application\DataProcessor\Write\Destination\StringDestination;
use App\Application\DataProcessor\Write\Resource\ResourceInterface;

final class WriteProcess
{
    private DataWriterFactoryInterface $dataWriterFactory;

    private ReaderInterface $reader;

    private ?DescriptorInterface $descriptor;

    private $onReaderError;

    public function __construct(DataWriterFactoryInterface $dataWriterFactory, ReaderInterface $reader, ?DescriptorInterface $descriptor = null, ?callable $onReaderError = null)
    {
        $this->dataWriterFactory = $dataWriterFactory;
        $this->reader = $reader;
        $this->descriptor = $descriptor;
        $this->onReaderError = $onReaderError;
    }

    public function withDescriptor(DescriptorInterface $descriptor): self
    {
        return new self($this->dataWriterFactory, $this->reader, $descriptor, $this->onReaderError);
    }

    public function withReaderErrorCallback(callable $onReaderError): self
    {
        return new self($this->dataWriterFactory, $this->reader, $this->descriptor, $onReaderError);
    }

    public function write(string $format, DestinationInterface $destination): DestinationInterface
    {
        return $this->dataWriterFactory->toDestination($format, $this->createResource(), $destination)->write();
    }

    public function writeToFile(string $format, string $filename, array $options = []): void
    {
        $this->dataWriterFactory->toFile($format, $this->createResource(), $filename, $options)->write();
    }

    public function writeToString(string $format, array $options = []): string
    {
        $destination = $this->dataWriterFactory->toString($format, $this->createResource(), $options)->write();
        assert($destination instanceof StringDestination);

        return $destination->string();
    }

    private function createResource(): ResourceInterface
    {
        return $this->reader->toWritableResource($this->descriptor, $this->onReaderError);
    }
}
