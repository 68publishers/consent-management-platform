<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings;

use DateTimeImmutable;
use App\Domain\Shared\ValueObject\Checksum;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\ConsentSettings\ValueObject\Settings;
use App\Domain\ConsentSettings\ValueObject\SettingsGroup;
use App\Domain\ConsentSettings\Event\ConsentSettingsAdded;
use App\Domain\ConsentSettings\ValueObject\ShortIdentifier;
use App\Domain\ConsentSettings\Event\ConsentSettingsCreated;
use App\Domain\ConsentSettings\ValueObject\ConsentSettingsId;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootTrait;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootInterface;

final class ConsentSettings implements AggregateRootInterface
{
	use AggregateRootTrait;

	private ConsentSettingsId $id;

	private ProjectId $projectId;

	private DateTimeImmutable $createdAt;

	private DateTimeImmutable $lastUpdateAt;

	private Checksum $checksum;

	private SettingsGroup $settings;

	private ShortIdentifier $shortIdentifier;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId                     $projectId
	 * @param \App\Domain\Shared\ValueObject\Checksum                       $checksum
	 * @param \App\Domain\ConsentSettings\ValueObject\Settings              $settings
	 * @param \App\Domain\ConsentSettings\CheckChecksumNotExistsInterface   $checkChecksumNotExists
	 * @param \App\Domain\ConsentSettings\ShortIdentifierGeneratorInterface $shortIdentifierGenerator
	 *
	 * @return static
	 */
	public static function create(ProjectId $projectId, Checksum $checksum, Settings $settings, CheckChecksumNotExistsInterface $checkChecksumNotExists, ShortIdentifierGeneratorInterface $shortIdentifierGenerator): self
	{
		$checkChecksumNotExists($projectId, $checksum);

		$consentSettings = new self();

		$consentSettings->recordThat(ConsentSettingsCreated::create(
			ConsentSettingsId::new(),
			$projectId,
			$checksum,
			SettingsGroup::fromItems([$settings]),
			$shortIdentifierGenerator->generate($projectId)
		));

		return $consentSettings;
	}

	/**
	 * {@inheritDoc}
	 */
	public function aggregateId(): AggregateId
	{
		return AggregateId::fromUuid($this->id->id());
	}

	/**
	 * @param \App\Domain\ConsentSettings\ValueObject\Settings $settings
	 *
	 * @return void
	 */
	public function addSettings(Settings $settings): void
	{
		if (!$this->settings->has($settings)) {
			$this->recordThat(ConsentSettingsAdded::create($this->id, $settings));
		}
	}

	/**
	 * @param \App\Domain\ConsentSettings\Event\ConsentSettingsCreated $event
	 *
	 * @return void
	 */
	protected function whenConsentSettingsCreated(ConsentSettingsCreated $event): void
	{
		$this->id = $event->consentSettingsId();
		$this->projectId = $event->projectId();
		$this->createdAt = $event->createdAt();
		$this->lastUpdateAt = $event->createdAt();
		$this->checksum = $event->checksum();
		$this->settings = $event->settings();
		$this->shortIdentifier = $event->shortIdentifier();
	}

	/**
	 * @param \App\Domain\ConsentSettings\Event\ConsentSettingsAdded $event
	 *
	 * @return void
	 */
	protected function whenConsentSettingsAdded(ConsentSettingsAdded $event): void
	{
		$this->lastUpdateAt = $event->createdAt();
		$this->settings = $this->settings->with($event->settings());
	}
}
