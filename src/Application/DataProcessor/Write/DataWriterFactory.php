<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write;

use App\Application\DataProcessor\Exception\WriterException;
use App\Application\DataProcessor\Write\Destination\DestinationInterface;
use App\Application\DataProcessor\Write\Destination\FileDestination;
use App\Application\DataProcessor\Write\Destination\StringDestination;
use App\Application\DataProcessor\Write\Resource\ResourceInterface;
use App\Application\DataProcessor\Write\Writer\WriterFactoryInterface;
use App\Application\DataProcessor\Write\Writer\WriterInterface;

final class DataWriterFactory implements DataWriterFactoryInterface
{
    /** @var WriterFactoryInterface[] */
    private array $writerFactories;

    /**
     * @param WriterFactoryInterface[] $writerFactories
     */
    public function __construct(array $writerFactories)
    {
        $this->writerFactories = (static fn (WriterFactoryInterface ...$writerFactories): array => $writerFactories)(...$writerFactories);
    }

    public function toDestination(string $format, ResourceInterface $resource, DestinationInterface $destination): WriterInterface
    {
        foreach ($this->writerFactories as $readerFactory) {
            if ($readerFactory->accepts($format, $destination)) {
                return $readerFactory->create($resource, $destination);
            }
        }

        throw WriterException::unresolvableDestination($format, $destination);
    }

    public function toFile(string $format, ResourceInterface $resource, string $filename, array $options = []): WriterInterface
    {
        return $this->toDestination($format, $resource, FileDestination::create($filename, $options));
    }

    public function toString(string $format, ResourceInterface $resource, array $options = []): WriterInterface
    {
        return $this->toDestination($format, $resource, StringDestination::create($options));
    }
}
