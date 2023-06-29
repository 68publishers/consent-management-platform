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
	public static function assignedToProject(string $projectId): self
	{
		return self::fromParameters([
			'assigned_project_id' => $projectId,
		]);
	}

	/**
	 * @param bool|string $booleanOrProjectId
	 */
	public function withPrivate($booleanOrProjectId): self
	{
		return $this->withParam('private', $booleanOrProjectId);
	}

	/**
	 * @return string|NULL
	 */
	public function assignedProjectId(): ?string
	{
		return $this->getParam('assigned_project_id');
	}

	/**
	 * @return bool|string
	 */
	public function private()
	{
		return $this->getParam('private') ?? FALSE;
	}
}
