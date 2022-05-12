<?php

declare(strict_types=1);

namespace App\Domain\Consent\Event;

use App\Domain\Shared\ValueObject\Checksum;
use App\Domain\Consent\ValueObject\ConsentId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class SettingsChecksumChanged extends AbstractDomainEvent
{
	private ConsentId $consentId;

	private Checksum $settingsChecksum;

	/**
	 * @param \App\Domain\Consent\ValueObject\ConsentId $consentId
	 * @param \App\Domain\Shared\ValueObject\Checksum   $settingsChecksum
	 *
	 * @return static
	 */
	public static function create(ConsentId $consentId, Checksum $settingsChecksum): self
	{
		$event = self::occur($consentId->toString(), [
			'settings_checksum' => $settingsChecksum->value(),
		]);

		$event->consentId = $consentId;
		$event->settingsChecksum = $settingsChecksum;

		return $event;
	}

	/**
	 * @return \App\Domain\Consent\ValueObject\ConsentId
	 */
	public function consentId(): ConsentId
	{
		return $this->consentId;
	}

	/**
	 * @return \App\Domain\Shared\ValueObject\Checksum
	 */
	public function settingsChecksum(): Checksum
	{
		return $this->settingsChecksum;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->consentId = ConsentId::fromUuid($this->aggregateId()->id());
		$this->settingsChecksum = Checksum::fromValue($parameters['settings_checksum']);
	}
}
