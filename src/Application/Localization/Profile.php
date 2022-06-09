<?php

declare(strict_types=1);

namespace App\Application\Localization;

final class Profile
{
	private string $locale;

	private string $name;

	private string $icon;

	private function __construct()
	{
	}

	/**
	 * @param string $locale
	 * @param string $name
	 * @param string $icon
	 *
	 * @return static
	 */
	public static function create(string $locale, string $name, string $icon): self
	{
		$profile = new self();
		$profile->locale = $locale;
		$profile->name = $name;
		$profile->icon = $icon;

		return $profile;
	}

	/**
	 * @return string
	 */
	public function locale(): string
	{
		return $this->locale;
	}

	/**
	 * @return string
	 */
	public function name(): string
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function icon(): string
	{
		return $this->icon;
	}
}
