<?php

declare(strict_types=1);

namespace App\Application\Helper;

use Carbon\Carbon;

final class Estimate
{
	public const REGEX = '/^(?:(?<years>\d+)y\s*)?(?:(?<months>\d+)m\s*)?(?:(?<days>\d+)d\s*)?(?:(?<hours>\d+)h\s*)?$/';

	private function __construct()
	{
	}

	/**
	 * @param string $mask
	 *
	 * @return bool
	 */
	public static function isMaskValid(string $mask): bool
	{
		$result = (int) preg_match_all(self::REGEX, $mask);

		return 0 !== $result;
	}

	/**
	 * @param string      $mask
	 * @param string      $locale
	 * @param string|NULL $fallbackLocale
	 *
	 * @return string
	 */
	public static function fromMask(string $mask, string $locale, ?string $fallbackLocale = NULL): string
	{
		if (0 === (int) preg_match_all(self::REGEX, $mask, $matches)) {
			return '';
		}

		$parts = array_filter([
			'year' => (int) ($matches['years'][0] ?? 0),
			'month' => (int) ($matches['months'][0] ?? 0),
			'day' => (int) ($matches['days'][0] ?? 0),
			'hour' => (int) ($matches['hours'][0] ?? 0),
		], static fn (int $number): bool => 0 < $number);

		if (0 >= count($parts)) {
			return '';
		}

		$carbon = Carbon::now()
			->locale(...array_filter(array_unique([$locale, $fallbackLocale])));

		foreach ($parts as $format => $number) {
			$parts[$format] = $carbon->translate($format, [], $number);
		}

		$shortDelimiter = $carbon->getTranslationMessage('list.0') ?? ' ';
		$longDelimiter = $carbon->getTranslationMessage('list.1') ?? $shortDelimiter;
		$lastPart = array_pop($parts);
		$expiration = implode($shortDelimiter, $parts);

		return empty($expiration) ? $lastPart : ($expiration . $longDelimiter . $lastPart);
	}
}
