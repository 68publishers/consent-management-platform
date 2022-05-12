<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings\Event;

use App\Domain\ConsentSettings\ValueObject\Settings;
use App\Domain\ConsentSettings\ValueObject\ConsentSettingsId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ConsentSettingsAdded extends AbstractDomainEvent
{
	private ConsentSettingsId $consentSettingsId;

	private Settings $settings;

	/**
	 * @param \App\Domain\ConsentSettings\ValueObject\ConsentSettingsId $consentSettingsId
	 * @param \App\Domain\ConsentSettings\ValueObject\Settings          $settings
	 *
	 * @return static
	 */
	public static function create(ConsentSettingsId $consentSettingsId, Settings $settings): self
	{
		$event = self::occur($consentSettingsId->toString(), [
			'settings' => $settings->values(),
		]);

		$event->consentSettingsId = $consentSettingsId;
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
	 * @return \App\Domain\ConsentSettings\ValueObject\Settings
	 */
	public function settings(): Settings
	{
		return $this->settings;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->consentSettingsId = ConsentSettingsId::fromUuid($this->aggregateId()->id());
		$this->settings = Settings::fromArray($parameters['settings']);
	}
}
