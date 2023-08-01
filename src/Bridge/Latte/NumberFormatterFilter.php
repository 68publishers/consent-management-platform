<?php

declare(strict_types=1);

namespace App\Bridge\Latte;

use NumberFormatter;
use SixtyEightPublishers\TranslationBridge\Localization\TranslatorLocalizerInterface;

final class NumberFormatterFilter
{
	private TranslatorLocalizerInterface $translatorLocalizer;

	/** @var array<string, NumberFormatter> */
	private array $formatters = [];

	public function __construct(TranslatorLocalizerInterface $translatorLocalizer)
	{
		$this->translatorLocalizer = $translatorLocalizer;
	}

	public function format(int|float $number, ?string $locale = NULL, int $style = NumberFormatter::DEFAULT_STYLE): string
	{
		$result = $this->getFormatter($locale ?? $this->translatorLocalizer->getLocale(), $style)->format($number);

		return FALSE === $result ? (string) $number : $result;
	}

	private function getFormatter(string $locale, int $style): NumberFormatter
	{
		$name = $locale . '::' . $style;

		return $this->formatters[$name] ?? $this->formatters[$name] = new NumberFormatter($locale, $style);
	}
}
