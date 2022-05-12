<?php

declare(strict_types=1);

namespace App\Domain\Consent\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class StoreConsentCommand extends AbstractCommand
{
	/**
	 * @param string $projectId
	 * @param string $userIdentifier
	 * @param string $settingsChecksum
	 * @param array  $consents
	 * @param array  $attributes
	 *
	 * @return static
	 */
	public static function create(string $projectId, string $userIdentifier, string $settingsChecksum, array $consents, array $attributes): self
	{
		return self::fromParameters([
			'project_id' => $projectId,
			'user_identifier' => $userIdentifier,
			'settings_checksum' => $settingsChecksum,
			'consents' => $consents,
			'attributes' => $attributes,
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

	/**
	 * @return string
	 */
	public function settingsChecksum(): string
	{
		return $this->getParam('settings_checksum');
	}

	/**
	 * @return array
	 */
	public function consents(): array
	{
		return $this->getParam('consents');
	}

	/**
	 * @return array
	 */
	public function attributes(): array
	{
		return $this->getParam('attributes');
	}
}
