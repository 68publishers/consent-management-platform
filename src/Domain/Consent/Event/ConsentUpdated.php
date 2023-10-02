<?php

declare(strict_types=1);

namespace App\Domain\Consent\Event;

use App\Domain\Consent\ValueObject\Attributes;
use App\Domain\Consent\ValueObject\ConsentId;
use App\Domain\Consent\ValueObject\Consents;
use App\Domain\Consent\ValueObject\Environment;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Shared\ValueObject\Checksum;
use DateTimeImmutable;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ConsentUpdated extends AbstractDomainEvent
{
    private ConsentId $consentId;

    private ProjectId $projectId;

    private ?Checksum $settingsChecksum = null;

    private Consents $consents;

    private Attributes $attributes;

    private ?Environment $environment = null;

    public static function create(
        ConsentId $consentId,
        ProjectId $projectId,
        ?Checksum $settingsChecksum,
        Consents $consents,
        Attributes $attributes,
        ?Environment $environment,
        ?DateTimeImmutable $createdAt = null,
    ): self {
        $event = self::occur($consentId->toString(), [
            'project_id' => $projectId->toString(),
            'settings_checksum' => $settingsChecksum?->value(),
            'consents' => $consents->values(),
            'attributes' => $attributes->values(),
            'environment' => $environment?->value(),
        ]);

        if (null !== $createdAt) {
            $event->createdAt = $createdAt;
        }

        $event->consentId = $consentId;
        $event->projectId = $projectId;
        $event->settingsChecksum = $settingsChecksum;
        $event->consents = $consents;
        $event->attributes = $attributes;
        $event->environment = $environment;

        return $event;
    }

    public function consentId(): ConsentId
    {
        return $this->consentId;
    }

    public function settingsChecksum(): ?Checksum
    {
        return $this->settingsChecksum;
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function consents(): Consents
    {
        return $this->consents;
    }

    public function attributes(): Attributes
    {
        return $this->attributes;
    }

    public function environment(): ?Environment
    {
        return $this->environment;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->consentId = ConsentId::fromUuid($this->aggregateId()->id());
        $this->projectId = ProjectId::fromString($parameters['project_id']);
        $this->settingsChecksum = isset($parameters['settings_checksum']) ? Checksum::fromValue($parameters['settings_checksum']) : null;
        $this->consents = Consents::fromArray($parameters['consents']);
        $this->attributes = Attributes::fromArray($parameters['attributes']);
        $this->environment = isset($parameters['environment']) ? Environment::fromValue($parameters['environment']) : null;
    }
}
