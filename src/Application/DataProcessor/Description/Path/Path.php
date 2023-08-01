<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description\Path;

use Countable;

final class Path implements Countable
{
    private array $parts;

    private function __construct() {}

    public static function fromParts(array $parts): self
    {
        $path = new self();
        $path->parts = $parts;

        return $path;
    }

    public static function fromString(string $pathString): self
    {
        $path = new self();
        $path->parts = !empty($pathString) ? explode('.', $pathString) : [];

        return $path;
    }

    public function shift(): ?string
    {
        return array_shift($this->parts);
    }

    public function parts(): array
    {
        return $this->parts;
    }

    public function count(): int
    {
        return count($this->parts);
    }
}
