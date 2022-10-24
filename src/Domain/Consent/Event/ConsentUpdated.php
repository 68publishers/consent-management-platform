<?php

declare(strict_types=1);

namespace App\Domain\Consent\Event;

use DateTimeImmutable;
use App\Domain\Shared\ValueObject\Checksum;
use App\Domain\Consent\ValueObject\Consents;
use App\Domain\Consent\ValueObject\ConsentId;
use App\Domain\Consent\ValueObject\Attributes;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ConsentUpdated extends AbstractDomainEvent
{
	private ConsentId $consentId;

	private ?Checksum $settingsChecksum = NULL;

	private Consents $consents;

	private Attributes $attributes;

	/**
	 * @param \App\Domain\Consent\ValueObject\ConsentId    $consentId
	 * @param \App\Domain\Shared\ValueObject\Checksum|NULL $settingsChecksum
	 * @param \App\Domain\Consent\ValueObject\Consents     $consents
	 * @param \App\Domain\Consent\ValueObject\Attributes   $attributes
	 * @param \DateTimeImmutable|NULL                      $createdAt
	 *
	 * @return static
	 */
	public static function create(ConsentId $consentId, ?Checksum $settingsChecksum, Consents $consents, Attributes $attributes, ?DateTimeImmutable $createdAt = NULL): self
	{
		$event = self::occur($consentId->toString(), [
			'settings_checksum' => NULL !== $settingsChecksum ? $settingsChecksum->value() : NULL,
			'consents' => $consents->values(),
			'attributes' => $attributes->values(),
		]);

		if (NULL !== $createdAt) {
			$event->createdAt = $createdAt;
		}

		$event->consentId = $consentId;
		$event->settingsChecksum = $settingsChecksum;
		$event->consents = $consents;
		$event->attributes = $attributes;

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
	public function settingsChecksum(): ?Checksum
	{
		return $this->settingsChecksum;
	}

	/**
	 * @return \App\Domain\Consent\ValueObject\Consents
	 */
	public function consents(): Consents
	{
		return $this->consents;
	}

	/**
	 * @return \App\Domain\Consent\ValueObject\Attributes
	 */
	public function attributes(): Attributes
	{
		return $this->attributes;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->consentId = ConsentId::fromUuid($this->aggregateId()->id());
		$this->settingsChecksum = isset($parameters['settings_checksum']) ? Checksum::fromValue($parameters['settings_checksum']) : NULL;
		$this->consents = Consents::fromArray($parameters['consents']);
		$this->attributes = Attributes::fromArray($parameters['attributes']);
	}
}
