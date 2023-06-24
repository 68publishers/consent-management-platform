<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Helper;

use InvalidArgumentException;

final class FlatResource
{
	private function __construct()
	{
	}

	/**
	 * @param array $flatArray
	 *
	 * @return array
	 */
	public static function toMultidimensionalArray(array $flatArray, string $separator = '.'): array
	{
		$result = [];

		foreach ($flatArray as $key => $value) {
			if (!is_string($key)) {
				$result[$key] = $value;

				continue;
			}

			$last = &$result;
			$keyParts = explode($separator, $key);

			while (NULL !== ($keyPart = array_shift($keyParts))) {
				if (!array_key_exists($keyPart, $last)) {
					$last[$keyPart] = [];
				}

				if (0 >= count($keyParts)) {
					$last[$keyPart] = $value;

					break;
				}

				$last = &$last[$keyPart];
			}
		}

		return $result;
	}

	/**
	 * @param array $multidimensionalArray
	 *
	 * @return array
	 */
	public static function toFlattArray(array $multidimensionalArray): array
	{
		return self::doFlatten($multidimensionalArray);
	}

	/**
	 * @param array  $array
	 * @param string $prefix
	 *
	 * @return array
	 */
	private static function doFlatten(array $array, string $prefix = ''): array
	{
		$results = [];

		foreach ($array as $key => $val) {
			if (is_array($val)) {
				$isList = !$val || array_keys($val) === range(0, count($val) - 1);

				if (!$isList) {
					$results[] = self::doFlatten($val, $prefix . $key . '.');

					continue;
				}

				if (!empty(array_filter($val, static fn ($v): bool => is_array($v)))) {
					throw new InvalidArgumentException(sprintf(
						'The array can not be flattened. The key %s contains an array of nested objects.',
						$prefix . $key
					));
				}
			}

			$results[] = [$prefix . $key => $val];
		}

		return array_merge(...$results);
	}
}
