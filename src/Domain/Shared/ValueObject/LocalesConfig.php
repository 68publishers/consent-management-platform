<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\MissingLocaleException;

final class LocalesConfig
{
    private Locales $locales;

    private Locale $defaultLocale;

    private function __construct() {}

    public static function create(Locales $locales, Locale $defaultLocale): self
    {
        if (!$locales->has($defaultLocale)) {
            throw MissingLocaleException::missingLocale($locales, $defaultLocale);
        }

        $localesConfig = new self();
        $localesConfig->locales = $locales;
        $localesConfig->defaultLocale = $defaultLocale;

        return $localesConfig;
    }

    public function locales(): Locales
    {
        return $this->locales;
    }

    public function defaultLocale(): Locale
    {
        return $this->defaultLocale;
    }

    public function equals(self $localesConfig): bool
    {
        return $this->locales()->equals($localesConfig->locales()) && $this->defaultLocale()->equals($localesConfig->defaultLocale());
    }
}
