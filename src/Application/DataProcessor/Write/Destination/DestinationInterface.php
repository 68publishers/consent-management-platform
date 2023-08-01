<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Destination;

interface DestinationInterface
{
    public function options(): array;

    public function __toString(): string;
}
