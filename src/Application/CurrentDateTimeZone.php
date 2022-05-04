<?php

declare(strict_types=1);

namespace App\Application;

use DateTimeZone;

final class CurrentDateTimeZone
{
	private static ?DateTimeZone $dateTimeZone = NULL;

	private function __construct()
	{
	}

	/**
	 * @param \DateTimeZone $dateTimeZone
	 *
	 * @return void
	 */
	public static function set(DateTimeZone $dateTimeZone): void
	{
		self::$dateTimeZone = $dateTimeZone;
	}

	/**
	 * @return \DateTimeZone
	 */
	public static function get(): DateTimeZone
	{
		if (NULL === self::$dateTimeZone) {
			self::set(new DateTimeZone('UTC'));
		}

		return self::$dateTimeZone;
	}
}
