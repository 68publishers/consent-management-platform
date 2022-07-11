<?php

declare(strict_types=1);

namespace App\ReadModel\ConsentSettings;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns ConsentSettingsView
 */
final class GetConsentSettingsByIdAndProjectIdQuery extends AbstractQuery
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
			'projectId' => $projectId,
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
		return $this->getParam('projectId');
	}
}
