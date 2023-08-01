<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read;

use App\Application\DataProcessor\Read\Reader\ReaderInterface;
use App\Application\DataProcessor\Read\Resource\ResourceInterface;

interface DataReaderFactoryInterface
{
    public function fromResource(string $format, ResourceInterface $resource): ReaderInterface;

    public function fromFile(string $format, string $filename, array $options = []): ReaderInterface;

    public function fromString(string $format, string $string, array $options = []): ReaderInterface;

    public function fromArray(array $data, array $options = []): ReaderInterface;
}
