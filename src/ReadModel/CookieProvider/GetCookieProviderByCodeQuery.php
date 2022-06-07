<?php

declare(strict_types=1);

namespace App\ReadModel\CookieProvider;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns CookieProviderView or NULL
 */
final class GetCookieProviderByCodeQuery extends AbstractQuery
{
	/**
	 * @param string $code
	 *
	 * @return static
	 */
	public static function create(string $code): self
	{
		return self::fromParameters([
			'code' => $code,
		]);
	}

	/**
	 * @return string
	 */
	public function code(): string
	{
		return $this->getParam('code');
	}
}
