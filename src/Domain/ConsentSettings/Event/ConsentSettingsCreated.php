<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings\Event;

use App\Domain\Shared\ValueObject\Checksum;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\ConsentSettings\ValueObject\SettingsGroup;
use App\Domain\ConsentSettings\ValueObject\ConsentSettingsId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ConsentSettingsCreated extends AbstractDomainEvent
{
	private ConsentSettingsId $consentSettingsId;

	private ProjectId $projectId;

	private Checksum $checksum;

	private SettingsGroup $settings;

	/**
	 * @param \App\Domain\ConsentSettings\ValueObject\ConsentSettingsId $consentSettingsId
	 * @param \App\Domain\Project\ValueObject\ProjectId                 $projectId
	 * @param \App\Domain\Shared\ValueObject\Checksum                   $checksum
	 * @param \App\Domain\ConsentSettings\ValueObject\SettingsGroup     $settings
	 *
	 * @return static
	 */
	public static function create(ConsentSettingsId $consentSettingsId, ProjectId $projectId, Checksum $checksum, SettingsGroup $settings): self
	{
		$event = self::occur($consentSettingsId->toString(), [
			'project_id' => $projectId->toString(),
			'checksum' => $checksum->value(),
			'settings' => $settings->toArray(),
		]);

		$event->consentSettingsId = $consentSettingsId;
		$event->projectId = $projectId;
		$event->checksum = $checksum;
		$event->settings = $settings;

		return $event;
	}

	/**
	 * @return \App\Domain\ConsentSettings\ValueObject\ConsentSettingsId
	 */
	public function consentSettingsId(): ConsentSettingsId
	{
		return $this->consentSettingsId;
	}

	/**
	 * @return \App\Domain\Project\ValueObject\ProjectId
	 */
	public function projectId(): ProjectId
	{
		return $this->projectId;
	}

	/**
	 * @return \App\Domain\Shared\ValueObject\Checksum
	 */
	public function checksum(): Checksum
	{
		return $this->checksum;
	}

	/**
	 * @return \App\Domain\ConsentSettings\ValueObject\SettingsGroup
	 */
	public function settings(): SettingsGroup
	{
		return $this->settings;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->consentSettingsId = ConsentSettingsId::fromUuid($this->aggregateId()->id());
		$this->projectId = ProjectId::fromString($parameters['project_id']);
		$this->checksum = Checksum::fromValue($parameters['checksum']);
		$this->settings = SettingsGroup::reconstitute($parameters['settings']);
	}
}
