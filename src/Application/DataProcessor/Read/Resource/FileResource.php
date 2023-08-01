<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Resource;

use App\Application\DataProcessor\Exception\IoException;

final class FileResource implements ResourceInterface
{
    private string $filename;

    private array $options = [];

    private function __construct() {}

    /**
     * @return static
     */
    public static function create(string $filename, array $options = []): self
    {
        $resource = new self();
        $resource->filename = $filename;
        $resource->options = $options;

        return $resource;
    }

    public function filename(): string
    {
        return $this->filename;
    }

    public function extension(): ?string
    {
        $pathInfo = pathinfo($this->filename());

        return $pathInfo['extension'] ?? null;
    }

    public function exists(): bool
    {
        return file_exists($this->filename());
    }

    public function content(): string
    {
        if (!$this->exists()) {
            throw IoException::fileNotFound($this->filename());
        }

        $content = @file_get_contents($this->filename());

        if (false === $content) {
            throw IoException::fileNotReadable($this->filename());
        }

        return $content;
    }
    
    public function options(): array
    {
        return $this->options;
    }

    public function __toString(): string
    {
        return sprintf(
            'FILE(%s)',
            $this->filename(),
        );
    }
}
