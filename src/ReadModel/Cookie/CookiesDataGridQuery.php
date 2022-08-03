<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

use App\ReadModel\AbstractDataGridQuery;

/**
 * Returns CookieDataGridItemView[]
 */
final class CookiesDataGridQuery extends AbstractDataGridQuery
{
	/**
	 * @param string|NULL $locale
	 *
	 * @return static
	 */
	public static function create(?string $locale): self
	{
		return self::fromParameters([
			'locale' => $locale,
		]);
	}

	/**
	 * @return string|NULL
	 */
	public function locale(): ?string
	{
		return $this->getParam('locale');
	}

	/**
	 * @return string|NULL
	 */
	public function cookieProviderId(): ?string
	{
		return $this->getParam('cookie_provider_id');
	}

	/**
	 * @return bool
	 */
	public function includeProjectsData(): bool
	{
		return $this->getParam('include_projects_data') ?? FALSE;
	}

	/**
	 * @param string $cookieProviderId
	 *
	 * @return $this
	 */
	public function withCookieProviderId(string $cookieProviderId): self
	{
		return $this->withParam('cookie_provider_id', $cookieProviderId);
	}

	/**
	 * @param bool $includeProjectsData
	 *
	 * @return $this
	 */
	public function withProjectsData(bool $includeProjectsData): self
	{
		return $this->withParam('include_projects_data', $includeProjectsData);
	}
}
