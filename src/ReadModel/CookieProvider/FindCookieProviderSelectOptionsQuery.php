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
	 * @param bool $privateAllowed
	 *
	 * @return $this
	 */
	public function withPrivate(bool $privateAllowed): self
	{
		return $this->withParam('private_allowed', $privateAllowed);
	}

	/**
	 * @return string|NULL
	 */
	public function projectId(): ?string
	{
		return $this->getParam('project_id');
	}

	/**
	 * @return bool
	 */
	public function privateAllowed(): bool
	{
		return $this->getParam('private_allowed') ?? FALSE;
	}
}
