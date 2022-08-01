<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractBatchedQuery;

/**
 * Returns CookieApiView[]
 */
final class FindCookiesForApiQuery extends AbstractBatchedQuery
{
	/**
	 * @param string      $projectId
	 * @param string|NULL $locale
	 *
	 * @return static
	 */
	public static function create(string $projectId, ?string $locale = NULL): self
	{
		return self::fromParameters([
			'project_id' => $projectId,
			'locale' => $locale,
		]);
	}

	/**
	 * @param string[] $categoryCodes
	 *
	 * @return $this
	 */
	public function withCategoryCodes(array $categoryCodes): self
	{
		return $this->withParam('category_codes', $categoryCodes);
	}

	/**
	 * @return string
	 */
	public function projectId(): string
	{
		return $this->getParam('project_id');
	}

	/**
	 * @return string|NULL
	 */
	public function locale(): ?string
	{
		return $this->getParam('locale');
	}

	/**
	 * @return string[]|NULL
	 */
	public function categoryCodes(): ?array
	{
		return $this->getParam('category_codes');
	}
}
