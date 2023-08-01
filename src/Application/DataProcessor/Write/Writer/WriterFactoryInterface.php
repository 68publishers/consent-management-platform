<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Writer;

use App\Application\DataProcessor\Exception\WriterException;
use App\Application\DataProcessor\Write\Destination\DestinationInterface;
use App\Application\DataProcessor\Write\Resource\ResourceInterface;

interface WriterFactoryInterface
{
    public function accepts(string $format, DestinationInterface $destination): bool;

    /**
     * @throws WriterException
     */
    public function create(ResourceInterface $resource, DestinationInterface $destination): WriterInterface;
}
