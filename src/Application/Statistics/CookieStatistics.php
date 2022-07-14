<?php

declare(strict_types=1);

namespace App\Application\Statistics;

final class CookieStatistics
{
	private int $numberOfProviders;

	private int $numberOfCommonCookies;

	private int $numberOfPrivateCookies;

	private function __construct()
	{
	}

	/**
	 * @param int $numberOfProviders
	 * @param int $numberOfCommonCookies
	 * @param int $numberOfPrivateCookies
	 *
	 * @return static
	 */
	public static function create(int $numberOfProviders, int $numberOfCommonCookies, int $numberOfPrivateCookies): self
	{
		$cookieStatistics = new self();
		$cookieStatistics->numberOfProviders = $numberOfProviders;
		$cookieStatistics->numberOfCommonCookies = $numberOfCommonCookies;
		$cookieStatistics->numberOfPrivateCookies = $numberOfPrivateCookies;

		return $cookieStatistics;
	}

	/**
	 * @return int
	 */
	public function numberOfProviders(): int
	{
		return $this->numberOfProviders;
	}

	/**
	 * @return int
	 */
	public function numberOfCommonCookies(): int
	{
		return $this->numberOfCommonCookies;
	}

	/**
	 * @return int
	 */
	public function numberOfPrivateCookies(): int
	{
		return $this->numberOfPrivateCookies;
	}
}
