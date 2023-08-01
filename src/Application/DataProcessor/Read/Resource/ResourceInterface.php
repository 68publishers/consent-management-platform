<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Resource;

interface ResourceInterface
{
    public function options(): array;

    public function __toString(): string;
}
