<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Resource;

use App\Application\DataProcessor\Description\DescriptorInterface;
use App\Application\DataProcessor\Read\Reader\ReaderInterface;

final class ReaderResource implements ResourceInterface
{
    private ReaderInterface $reader;

    private ?DescriptorInterface $descriptor;

    private $onError;

    public function __construct(ReaderInterface $reader, ?DescriptorInterface $descriptor = null, ?callable $onError = null)
    {
        $this->reader = $reader;
        $this->descriptor = $descriptor;
        $this->onError = $onError;
    }

    public function rows(): iterable
    {
        return $this->reader->read($this->descriptor, $this->onError);
    }

    public function descriptor(): ?DescriptorInterface
    {
        return $this->descriptor;
    }

    public function __toString(): string
    {
        return sprintf(
            'READER(%s%s)',
            get_class($this->reader),
            null !== $this->descriptor ? (', ' . get_class($this->descriptor)) : '',
        );
    }
}
