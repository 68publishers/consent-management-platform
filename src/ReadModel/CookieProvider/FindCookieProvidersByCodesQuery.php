<?php

declare(strict_types=1);

namespace App\ReadModel\CookieProvider;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns CookieProviderView[]
 */
final class FindCookieProvidersByCodesQuery extends AbstractQuery
{
	/**
	 * @param string[] $codes
	 *
	 * @return static
	 */
	public static function create(array $codes): self
	{
		return self::fromParameters([
			'codes' => $codes,
		]);
	}

	/**
	 * @return string[]
	 */
	public function codes(): array
	{
		return $this->getParam('codes');
	}
}
