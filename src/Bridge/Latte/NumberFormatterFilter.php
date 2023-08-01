<?php

declare(strict_types=1);

namespace App\Bridge\Latte;

use NumberFormatter;
use SixtyEightPublishers\TranslationBridge\Localization\TranslatorLocalizerInterface;

final class NumberFormatterFilter
{
    /** @var array<string, NumberFormatter> */
    private array $formatters = [];

    public function __construct(
        private readonly TranslatorLocalizerInterface $translatorLocalizer,
    ) {}

    public function format(int|float $number, ?string $locale = null, int $style = NumberFormatter::DEFAULT_STYLE): string
    {
        $result = $this->getFormatter($locale ?? $this->translatorLocalizer->getLocale(), $style)->format($number);

        return false === $result ? (string) $number : $result;
    }

    private function getFormatter(string $locale, int $style): NumberFormatter
    {
        $name = $locale . '::' . $style;

        return $this->formatters[$name] ?? $this->formatters[$name] = new NumberFormatter($locale, $style);
    }
}
