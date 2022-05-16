<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

final class GetConsentByIdAndProjectIdQuery extends AbstractQuery
{
	/**
	 * @param string $id
	 * @param string $projectId
	 *
	 * @return static
	 */
	public static function create(string $id, string $projectId): self
	{
		return self::fromParameters([
			'id' => $id,
			'project_id' => $projectId,
		]);
	}

	/**
	 * @return string
	 */
	public function id(): string
	{
		return $this->getParam('id');
	}

	/**
	 * @return string
	 */
	public function projectId(): string
	{
		return $this->getParam('project_id');
	}
}
