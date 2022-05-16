<?php

declare(strict_types=1);

namespace App\Domain\Consent\Event;

use App\Domain\Shared\ValueObject\Checksum;
use App\Domain\Consent\ValueObject\Consents;
use App\Domain\Consent\ValueObject\ConsentId;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Consent\ValueObject\Attributes;
use App\Domain\Consent\ValueObject\UserIdentifier;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ConsentCreated extends AbstractDomainEvent
{
	private ConsentId $consentId;

	private ProjectId $projectId;

	private UserIdentifier $userIdentifier;

	private ?Checksum $settingsChecksum = NULL;

	private Consents $consents;

	private Attributes $attributes;

	/**
	 * @param \App\Domain\Consent\ValueObject\ConsentId      $consentId
	 * @param \App\Domain\Project\ValueObject\ProjectId      $projectId
	 * @param \App\Domain\Consent\ValueObject\UserIdentifier $userIdentifier
	 * @param \App\Domain\Shared\ValueObject\Checksum|NULL   $settingsChecksum
	 * @param \App\Domain\Consent\ValueObject\Consents       $consents
	 * @param \App\Domain\Consent\ValueObject\Attributes     $attributes
	 *
	 * @return static
	 */
	public static function create(ConsentId $consentId, ProjectId $projectId, UserIdentifier $userIdentifier, ?Checksum $settingsChecksum, Consents $consents, Attributes $attributes): self
	{
		$event = self::occur($consentId->toString(), [
			'project_id' => $projectId->toString(),
			'user_identifier' => $userIdentifier->value(),
			'settings_checksum' => NULL !== $settingsChecksum ? $settingsChecksum->value() : NULL,
			'consents' => $consents->values(),
			'attributes' => $attributes->values(),
		]);

		$event->consentId = $consentId;
		$event->projectId = $projectId;
		$event->userIdentifier = $userIdentifier;
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
	 * @return \App\Domain\Project\ValueObject\ProjectId
	 */
	public function projectId(): ProjectId
	{
		return $this->projectId;
	}

	/**
	 * @return \App\Domain\Consent\ValueObject\UserIdentifier
	 */
	public function userIdentifier(): UserIdentifier
	{
		return $this->userIdentifier;
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
		$this->projectId = ProjectId::fromString($parameters['project_id']);
		$this->userIdentifier = UserIdentifier::fromValue($parameters['user_identifier']);
		$this->settingsChecksum = isset($parameters['settings_checksum']) ? Checksum::fromValue($parameters['settings_checksum']) : NULL;
		$this->consents = Consents::fromArray($parameters['consents']);
		$this->attributes = Attributes::fromArray($parameters['attributes']);
	}
}
