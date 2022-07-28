<?php

declare(strict_types=1);

namespace App\Application\DataReader\Utils;

final class ResourceUtils
{
	private function __construct()
	{
	}

	/**
	 * @param array $flatArray
	 *
	 * @return array
	 */
	public static function toMultidimensionalArray(array $flatArray): array
	{
		$result = [];

		foreach ($flatArray as $key => $value) {
			if (!is_string($key)) {
				$result[$key] = $value;

				continue;
			}

			$last = &$result;
			$keyParts = explode('.', $key);

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
}
