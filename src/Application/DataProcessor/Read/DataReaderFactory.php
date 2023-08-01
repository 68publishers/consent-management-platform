<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read;

use App\Application\DataProcessor\Exception\ReaderException;
use App\Application\DataProcessor\Read\Reader\ReaderFactoryInterface;
use App\Application\DataProcessor\Read\Reader\ReaderInterface;
use App\Application\DataProcessor\Read\Resource\ArrayResource;
use App\Application\DataProcessor\Read\Resource\FileResource;
use App\Application\DataProcessor\Read\Resource\ResourceInterface;
use App\Application\DataProcessor\Read\Resource\StringResource;

final class DataReaderFactory implements DataReaderFactoryInterface
{
    /** @var array<ReaderFactoryInterface> */
    private array $readerFactories;

    /**
     * @param array<ReaderFactoryInterface> $readerFactories
     */
    public function __construct(array $readerFactories)
    {
        $this->readerFactories = (static fn (ReaderFactoryInterface ...$readerFactories): array => $readerFactories)(...$readerFactories);
    }

    public function fromResource(string $format, ResourceInterface $resource): ReaderInterface
    {
        foreach ($this->readerFactories as $readerFactory) {
            if ($readerFactory->accepts($format, $resource)) {
                return $readerFactory->create($resource);
            }
        }

        throw ReaderException::unresolvableResource($format, $resource);
    }

    public function fromFile(string $format, string $filename, array $options = []): ReaderInterface
    {
        return $this->fromResource($format, FileResource::create($filename, $options));
    }

    public function fromString(string $format, string $string, array $options = []): ReaderInterface
    {
        return $this->fromResource($format, StringResource::create($string, $options));
    }

    public function fromArray(array $data, array $options = []): ReaderInterface
    {
        return $this->fromResource('array', ArrayResource::create($data, $options));
    }
}
