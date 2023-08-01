<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Writer;

use App\Application\DataProcessor\Write\Destination\DestinationInterface;

interface WriterInterface
{
    public function write(): DestinationInterface;
}
