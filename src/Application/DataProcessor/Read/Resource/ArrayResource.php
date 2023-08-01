<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Resource;

final class ArrayResource implements ResourceInterface
{
    private array $data;

    private array $options = [];

    private function __construct() {}

    /**
     * @return static
     */
    public static function create(array $data, array $options = []): self
    {
        $resource = new self();
        $resource->data = $data;
        $resource->options = $options;

        return $resource;
    }

    public function data(): array
    {
        return $this->data;
    }
    
    public function options(): array
    {
        return $this->options;
    }

    public function __toString(): string
    {
        return 'ARRAY(...)';
    }
}
