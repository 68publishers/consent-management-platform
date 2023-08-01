<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write;

use App\Application\DataProcessor\Write\Destination\DestinationInterface;
use App\Application\DataProcessor\Write\Resource\ResourceInterface;
use App\Application\DataProcessor\Write\Writer\WriterInterface;

interface DataWriterFactoryInterface
{
    public function toDestination(string $format, ResourceInterface $resource, DestinationInterface $destination): WriterInterface;

    public function toFile(string $format, ResourceInterface $resource, string $filename, array $options = []): WriterInterface;

    public function toString(string $format, ResourceInterface $resource, array $options = []): WriterInterface;
}
