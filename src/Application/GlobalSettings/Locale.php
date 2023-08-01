<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

final class Locale
{
    private string $code;

    private string $name;

    private function __construct() {}

    public static function create(string $code, string $name): self
    {
        $locale = new self();
        $locale->code = $code;
        $locale->name = $name;

        return $locale;
    }

    public static function unknown(): self
    {
        return self::create('unknown', 'unknown');
    }

    public function code(): string
    {
        return $this->code;
    }

    public function name(): string
    {
        return $this->name;
    }
}
