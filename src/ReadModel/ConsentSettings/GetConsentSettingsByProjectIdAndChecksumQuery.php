<?php

declare(strict_types=1);

namespace App\ReadModel\ConsentSettings;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns ConsentSettingsView
 */
final class GetConsentSettingsByProjectIdAndChecksumQuery extends AbstractQuery
{
	/**
	 * @param string $projectId
	 * @param string $checksum
	 *
	 * @return static
	 */
	public static function create(string $projectId, string $checksum): self
	{
		return self::fromParameters([
			'projectId' => $projectId,
			'checksum' => $checksum,
		]);
	}

	/**
	 * @return string
	 */
	public function projectId(): string
	{
		return $this->getParam('projectId');
	}

	/**
	 * @return string
	 */
	public function checksum(): string
	{
		return $this->getParam('checksum');
	}
}
