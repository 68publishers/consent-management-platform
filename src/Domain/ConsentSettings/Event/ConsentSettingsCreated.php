<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings\Event;

use App\Domain\ConsentSettings\ValueObject\ConsentSettingsId;
use App\Domain\ConsentSettings\ValueObject\SettingsGroup;
use App\Domain\ConsentSettings\ValueObject\ShortIdentifier;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Shared\ValueObject\Checksum;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ConsentSettingsCreated extends AbstractDomainEvent
{
    private ConsentSettingsId $consentSettingsId;

    private ProjectId $projectId;

    private Checksum $checksum;

    private SettingsGroup $settings;

    private ShortIdentifier $shortIdentifier;

    public static function create(ConsentSettingsId $consentSettingsId, ProjectId $projectId, Checksum $checksum, SettingsGroup $settings, ShortIdentifier $shortIdentifier): self
    {
        $event = self::occur($consentSettingsId->toString(), [
            'project_id' => $projectId->toString(),
            'checksum' => $checksum->value(),
            'settings' => $settings->toArray(),
            'short_identifier' => $shortIdentifier->value(),
        ]);

        $event->consentSettingsId = $consentSettingsId;
        $event->projectId = $projectId;
        $event->checksum = $checksum;
        $event->settings = $settings;
        $event->shortIdentifier = $shortIdentifier;

        return $event;
    }

    public function consentSettingsId(): ConsentSettingsId
    {
        return $this->consentSettingsId;
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function checksum(): Checksum
    {
        return $this->checksum;
    }

    public function settings(): SettingsGroup
    {
        return $this->settings;
    }

    public function shortIdentifier(): ShortIdentifier
    {
        return $this->shortIdentifier;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->consentSettingsId = ConsentSettingsId::fromUuid($this->aggregateId()->id());
        $this->projectId = ProjectId::fromString($parameters['project_id']);
        $this->checksum = Checksum::fromValue($parameters['checksum']);
        $this->settings = SettingsGroup::reconstitute($parameters['settings']);
        $this->shortIdentifier = ShortIdentifier::fromValue($parameters['short_identifier']);
    }
}
