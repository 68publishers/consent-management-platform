<?php

declare(strict_types=1);

namespace App\Domain\Consent;

use DateTimeImmutable;
use App\Domain\Shared\ValueObject\Checksum;
use App\Domain\Consent\Event\ConsentCreated;
use App\Domain\Consent\ValueObject\Consents;
use App\Domain\Consent\Event\ConsentsChanged;
use App\Domain\Consent\ValueObject\ConsentId;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Consent\ValueObject\Attributes;
use App\Domain\Consent\Event\AttributesChanged;
use App\Domain\Consent\ValueObject\UserIdentifier;
use App\Domain\Consent\Command\StoreConsentCommand;
use App\Domain\Consent\Event\SettingsChecksumChanged;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootTrait;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootInterface;

final class Consent implements AggregateRootInterface
{
	use AggregateRootTrait;

	private ConsentId $id;

	private ProjectId $projectId;

	private DateTimeImmutable $createdAt;

	private DateTimeImmutable $lastUpdateAt;

	private UserIdentifier $userIdentifier;

	private Checksum $settingsChecksum;

	private Consents $consents;

	private Attributes $attributes;

	/**
	 * @param \App\Domain\Consent\Command\StoreConsentCommand           $command
	 * @param \App\Domain\Consent\CheckUserIdentifierNotExistsInterface $checkUserIdentifierNotExists
	 *
	 * @return static
	 */
	public static function create(StoreConsentCommand $command, CheckUserIdentifierNotExistsInterface $checkUserIdentifierNotExists): self
	{
		$consentId = ConsentId::new();
		$projectId = ProjectId::fromString($command->projectId());
		$userIdentifier = UserIdentifier::fromValue($command->userIdentifier());
		$settingsChecksum = Checksum::fromValue($command->settingsChecksum());
		$consents = Consents::fromArray($command->consents());
		$attributes = Attributes::fromArray($command->attributes());

		$checkUserIdentifierNotExists($userIdentifier, $projectId);

		$consent = new self();

		$consent->recordThat(ConsentCreated::create($consentId, $projectId, $userIdentifier, $settingsChecksum, $consents, $attributes));

		return $consent;
	}

	/**
	 * @param \App\Domain\Consent\Command\StoreConsentCommand $command
	 *
	 * @return void
	 */
	public function update(StoreConsentCommand $command): void
	{
		$consents = Consents::fromArray($command->consents());
		$attributes = Attributes::fromArray($command->attributes());
		$settingsChecksum = Checksum::fromValue($command->settingsChecksum());

		if (!$this->consents->equals($consents)) {
			$this->recordThat(ConsentsChanged::create($this->id, $consents));
		}

		if (!$this->attributes->equals($attributes)) {
			$this->recordThat(AttributesChanged::create($this->id, $attributes));
		}

		if (!$this->settingsChecksum->equals($settingsChecksum)) {
			$this->recordThat(SettingsChecksumChanged::create($this->id, $settingsChecksum));
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function aggregateId(): AggregateId
	{
		return AggregateId::fromUuid($this->id->id());
	}

	/**
	 * @param \App\Domain\Consent\Event\ConsentCreated $event
	 *
	 * @return void
	 */
	protected function whenConsentCreated(ConsentCreated $event): void
	{
		$this->id = $event->consentId();
		$this->projectId = $event->projectId();
		$this->createdAt = $event->createdAt();
		$this->lastUpdateAt = $event->createdAt();
		$this->userIdentifier = $event->userIdentifier();
		$this->settingsChecksum = $event->settingsChecksum();
		$this->consents = $event->consents();
		$this->attributes = $event->attributes();
	}

	/**
	 * @param \App\Domain\Consent\Event\AttributesChanged $event
	 *
	 * @return void
	 */
	protected function whenAttributesChanged(AttributesChanged $event): void
	{
		$this->attributes = $event->attributes();
		$this->lastUpdateAt = $event->createdAt();
	}

	/**
	 * @param \App\Domain\Consent\Event\ConsentsChanged $event
	 *
	 * @return void
	 */
	protected function whenConsentsChanged(ConsentsChanged $event): void
	{
		$this->consents = $event->consents();
		$this->lastUpdateAt = $event->createdAt();
	}

	/**
	 * @param \App\Domain\Consent\Event\SettingsChecksumChanged $event
	 *
	 * @return void
	 */
	protected function whenSettingsChecksumChanged(SettingsChecksumChanged $event): void
	{
		$this->settingsChecksum = $event->settingsChecksum();
		$this->lastUpdateAt = $event->createdAt();
	}
}
