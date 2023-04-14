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

	/**
	 * @param int|float $number
	 */
	public function format($number, ?string $locale = NULL, int $style = NumberFormatter::DEFAULT_STYLE): string
	{
		assert(is_int($number) || is_float($number));

		$result = $this->getFormatter($locale ?? $this->translatorLocalizer->getLocale(), $style)->format($number);

		return FALSE === $result ? $number : $result;
	}

	private function getFormatter(string $locale, int $style): NumberFormatter
	{
		$name = $locale . '::' . $style;

		return $this->formatters[$name] ?? $this->formatters[$name] = new NumberFormatter($locale, $style);
	}
}
