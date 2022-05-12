<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class StoreConsentSettingsCommand extends AbstractCommand
{
	/**
	 * @param string $projectId
	 * @param string $checksum
	 * @param array  $settings
	 *
	 * @return static
	 */
	public static function create(string $projectId, string $checksum, array $settings): self
	{
		return self::fromParameters([
			'project_id' => $projectId,
			'checksum' => $checksum,
			'settings' => $settings,
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
	public function checksum(): string
	{
		return $this->getParam('checksum');
	}

	/**
	 * @return array
	 */
	public function setting(): array
	{
		return $this->getParam('settings');
	}
}
