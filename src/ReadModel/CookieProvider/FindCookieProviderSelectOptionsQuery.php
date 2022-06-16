<?php

declare(strict_types=1);

namespace App\ReadModel\CookieProvider;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns CookieProviderSelectOptionView[]
 */
final class FindCookieProviderSelectOptionsQuery extends AbstractQuery
{
	/**
	 * @return static
	 */
	public static function all(): self
	{
		return self::fromParameters([]);
	}

	/**
	 * @param string $projectId
	 *
	 * @return static
	 */
	public static function byProject(string $projectId): self
	{
		return self::fromParameters([
			'project_id' => $projectId,
		]);
	}

	/**
	 * @return string|NULL
	 */
	public function projectId(): ?string
	{
		return $this->getParam('project_id');
	}
}
