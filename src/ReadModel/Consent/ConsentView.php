<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use App\Domain\Consent\ValueObject\Attributes;
use App\Domain\Consent\ValueObject\ConsentId;
use App\Domain\Consent\ValueObject\Consents;
use App\Domain\Consent\ValueObject\UserIdentifier;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Shared\ValueObject\Checksum;
use DateTimeImmutable;
use DateTimeInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class ConsentView extends AbstractView
{
    public ConsentId $id;

    public ProjectId $projectId;

    public DateTimeImmutable $createdAt;

    public DateTimeImmutable $lastUpdateAt;

    public UserIdentifier $userIdentifier;

    public ?Checksum $settingsChecksum = null;

    public Consents $consents;

    public Attributes $attributes;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'projectId' => $this->projectId->toString(),
            'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
            'lastUpdateAt' => $this->lastUpdateAt->format(DateTimeInterface::ATOM),
            'userIdentifier' => $this->userIdentifier->value(),
            'settingsChecksum' => $this->settingsChecksum?->value(),
            'consents' => $this->consents->values(),
            'attributes' => $this->attributes->values(),
        ];
    }
}
