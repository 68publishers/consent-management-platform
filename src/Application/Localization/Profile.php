<?php

declare(strict_types=1);

namespace App\Application\Localization;

final class Profile
{
    private string $locale;

    private string $name;

    private string $icon;

    private function __construct() {}

    public static function create(string $locale, string $name, string $icon): self
    {
        $profile = new self();
        $profile->locale = $locale;
        $profile->name = $name;
        $profile->icon = $icon;

        return $profile;
    }

    public function locale(): string
    {
        return $this->locale;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function icon(): string
    {
        return $this->icon;
    }
}
