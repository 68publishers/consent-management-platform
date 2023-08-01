<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Destination;

final class StringDestination implements DestinationInterface
{
    private string $string;

    private array $options = [];

    private function __construct() {}

    public static function create(array $options = []): self
    {
        $destination = new self();
        $destination->string = '';
        $destination->options = $options;

        return $destination;
    }

    public function append(string $string): self
    {
        $destination = clone $this;
        $destination->string = $this->string . $string;

        return $destination;
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
