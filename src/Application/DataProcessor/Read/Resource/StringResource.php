<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Resource;

final class StringResource implements ResourceInterface
{
    private string $string;

    private array $options = [];

    private function __construct() {}

    public static function create(string $string, array $options = []): self
    {
        $resource = new self();
        $resource->string = $string;
        $resource->options = $options;

        return $resource;
    }

    public function string(): string
    {
        return $this->string;
    }

    public function options(): array
    {
        return $this->options;
    }

    public function __toString(): string
    {
        return 'STRING(...)';
    }
}
