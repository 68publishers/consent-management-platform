<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

final class GetConsentByProjectIdAndUserIdentifierQuery extends AbstractQuery
{
	/**
	 * @param string $projectId
	 * @param string $userIdentifier
	 *
	 * @return static
	 */
	public static function create(string $projectId, string $userIdentifier): self
	{
		return self::fromParameters([
			'project_id' => $projectId,
			'user_identifier' => $userIdentifier,
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
	 * @return string
	 */
	public function userIdentifier(): string
	{
		return $this->getParam('user_identifier');
	}
}
