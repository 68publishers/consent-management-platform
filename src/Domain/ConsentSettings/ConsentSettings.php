<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings;

use App\Domain\ConsentSettings\Event\ConsentSettingsAdded;
use App\Domain\ConsentSettings\Event\ConsentSettingsCreated;
use App\Domain\ConsentSettings\ValueObject\ConsentSettingsId;
use App\Domain\ConsentSettings\ValueObject\Settings;
use App\Domain\ConsentSettings\ValueObject\SettingsGroup;
use App\Domain\ConsentSettings\ValueObject\ShortIdentifier;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Shared\ValueObject\Checksum;
use DateTimeImmutable;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootInterface;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootTrait;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;

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
            $shortIdentifierGenerator->generate($projectId),
        ));

        return $consentSettings;
    }

    public function aggregateId(): AggregateId
    {
        return AggregateId::fromUuid($this->id->id());
    }

    public function addSettings(Settings $settings): void
    {
        if (!$this->settings->has($settings)) {
            $this->recordThat(ConsentSettingsAdded::create($this->id, $settings));
        }
    }

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

    protected function whenConsentSettingsAdded(ConsentSettingsAdded $event): void
    {
        $this->lastUpdateAt = $event->createdAt();
        $this->settings = $this->settings->with($event->settings());
    }
}
