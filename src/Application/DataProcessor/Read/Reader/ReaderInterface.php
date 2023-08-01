<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Reader;

use App\Application\DataProcessor\Description\DescriptorInterface;
use App\Application\DataProcessor\RowInterface;
use App\Application\DataProcessor\Write\Resource\ResourceInterface as WritableResource;

interface ReaderInterface
{
    /**
     * @return iterable<RowInterface>
     */
    public function read(?DescriptorInterface $descriptor = null, ?callable $onError = null): iterable;

    public function toWritableResource(?DescriptorInterface $descriptor = null, ?callable $onError = null): WritableResource;
}
