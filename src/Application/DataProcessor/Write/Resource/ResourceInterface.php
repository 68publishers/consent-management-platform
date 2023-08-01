<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Resource;

use App\Application\DataProcessor\Description\DescriptorInterface;
use App\Application\DataProcessor\RowInterface;

interface ResourceInterface
{
    /**
     * @return iterable<RowInterface>
     */
    public function rows(): iterable;

    public function descriptor(): ?DescriptorInterface;

    public function __toString(): string;
}
