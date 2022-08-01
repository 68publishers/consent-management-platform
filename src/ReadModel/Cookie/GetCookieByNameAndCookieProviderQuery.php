<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns CookieView or NULL
 */
final class GetCookieByNameAndCookieProviderQuery extends AbstractQuery
{
	/**
	 * @param string $name
	 * @param string $cookieProviderId
	 *
	 * @return static
	 */
	public static function create(string $name, string $cookieProviderId): self
	{
		return self::fromParameters([
			'name' => $name,
			'cookie_provider_id' => $cookieProviderId,
		]);
	}

	/**
	 * @return string
	 */
	public function name(): string
	{
		return $this->getParam('name');
	}

	/**
	 * @return string
	 */
	public function cookieProviderId(): string
	{
		return $this->getParam('cookie_provider_id');
	}
}
