<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

use App\ReadModel\AbstractDataGridQuery;

/**
 * Returns CookieItemView[]
 */
final class CookiesDataGridQuery extends AbstractDataGridQuery
{
	/**
	 * @param string      $cookieProviderId
	 * @param string|NULL $locale
	 *
	 * @return static
	 */
	public static function create(string $cookieProviderId, ?string $locale): self
	{
		return self::fromParameters([
			'cookie_provider_id' => $cookieProviderId,
			'locale' => $locale,
		]);
	}

	/**
	 * @return string
	 */
	public function cookieProviderId(): string
	{
		return $this->getParam('cookie_provider_id');
	}

	/**
	 * @return string|NULL
	 */
	public function locale(): ?string
	{
		return $this->getParam('locale');
	}
}
