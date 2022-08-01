<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns array of ProjectAccessibilityView
 */
final class FindAllProjectsWithPossibleAssociationWithCookieProviderQuery extends AbstractQuery
{
	/**
	 * @param string     $cookieProviderId
	 * @param array|NULL $projectCodes
	 *
	 * @return static
	 */
	public static function create(string $cookieProviderId, ?array $projectCodes): self
	{
		return self::fromParameters([
			'cookie_provider_id' => $cookieProviderId,
			'project_codes' => $projectCodes,
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
	 * @return string[]|NULL
	 */
	public function projectCodes(): ?array
	{
		return $this->getParam('project_codes');
	}
}
