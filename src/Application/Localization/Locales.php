<?php

declare(strict_types=1);

namespace App\Application\Localization;

use RuntimeException;
use SixtyEightPublishers\TranslationBridge\Localization\TranslatorLocalizerInterface;

final class Locales
{
    private array $cache = [];

    public function __construct(
        private readonly string $vendorDir,
        private readonly TranslatorLocalizerInterface $translatorLocalizer,
    ) {}

    public function get(?string $locale = null): array
    {
        $locale = $locale ?? $this->translatorLocalizer->getLocale();

        if (isset($this->cache[$locale])) {
            return $this->cache[$locale];
        }

        $locales = array_merge([$locale], $this->translatorLocalizer->getFallbackLocales());

        foreach ($locales as $loc) {
            $filename = sprintf(
                '%s/umpirsky/locale-list/data/%s/locales.php',
                $this->vendorDir,
                $loc,
            );

            if (file_exists($filename)) {
                return $this->cache[$locale] = include $filename;
            }
        }

        throw new RuntimeException(sprintf(
            'Can\'t resolve the list of locales for locale %s.',
            $locale,
        ));
    }
}
