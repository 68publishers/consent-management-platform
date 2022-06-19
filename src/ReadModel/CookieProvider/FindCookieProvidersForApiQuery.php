<?php

declare(strict_types=1);

namespace App\ReadModel\CookieProvider;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractBatchedQuery;

final class FindCookieProvidersForApiQuery extends AbstractBatchedQuery
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
}
