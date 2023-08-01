<?php

declare(strict_types=1);

namespace App\Application\Import;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class ImportOptions
{
    private string $describedObjectClassname;

    private ?string $authorId = null;

    private ?LoggerInterface $logger = null;

    private bool $async = false;

    private int $batchSize = 10;

    private function __construct() {}

    public static function create(string $describedObjectClassname): self
    {
        $options = new self();
        $options->describedObjectClassname = $describedObjectClassname;
        $options->authorId = null;

        return $options;
    }

    public function describedObjectClassname(): string
    {
        return $this->describedObjectClassname;
    }

    public function authorId(): ?string
    {
        return $this->authorId;
    }

    public function logger(): LoggerInterface
    {
        return $this->logger ?? new NullLogger();
    }

    public function async(): bool
    {
        return $this->async;
    }

    public function batchSize(): int
    {
        return $this->batchSize;
    }

    public function withAuthorId(?string $authorId): self
    {
        $options = clone $this;
        $options->authorId = $authorId;

        return $options;
    }

    public function withLogger(?LoggerInterface $logger): self
    {
        $options = clone $this;
        $options->logger = $logger;

        return $options;
    }

    public function withAsync(bool $async): self
    {
        $options = clone $this;
        $options->async = $async;

        return $options;
    }

    public function withBatchSize(int $batchSize): self
    {
        $options = clone $this;
        $options->batchSize = $batchSize;

        return $options;
    }
}
