<?php

declare(strict_types=1);

namespace App\Application\Mail;

final class Address
{
    private string $from;

    private ?string $name;

    private function __construct() {}

    /**
     * @return static
     */
    public static function create(string $from, ?string $name = null): self
    {
        $address = new self();
        $address->from = $from;
        $address->name = empty($name) ? null : $name;

        return $address;
    }

    public function from(): string
    {
        return $this->from;
    }

    public function name(): ?string
    {
        return $this->name;
    }
}
