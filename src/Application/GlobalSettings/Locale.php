<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

final class Locale
{
	private string $code;

	private string $name;

	private function __construct()
	{
	}

	/**
	 * @param string $code
	 * @param string $name
	 *
	 * @return static
	 */
	public static function create(string $code, string $name): self
	{
		$locale = new self();
		$locale->code = $code;
		$locale->name = $name;

		return $locale;
	}

	/**
	 * @return static
	 */
	public static function unknown(): self
	{
		return self::create('unknown', 'unknown');
	}

	/**
	 * @return string
	 */
	public function code(): string
	{
		return $this->code;
	}

	/**
	 * @return string
	 */
	public function name(): string
	{
		return $this->name;
	}
}
