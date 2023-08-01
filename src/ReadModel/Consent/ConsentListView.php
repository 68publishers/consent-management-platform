<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use App\Domain\Consent\ValueObject\ConsentId;
use App\Domain\Consent\ValueObject\UserIdentifier;
use App\Domain\ConsentSettings\ValueObject\ConsentSettingsId;
use App\Domain\ConsentSettings\ValueObject\ShortIdentifier;
use App\Domain\Shared\ValueObject\Checksum;
use DateTimeImmutable;
use DateTimeInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class ConsentListView extends AbstractView
{
    public ConsentId $id;

    public DateTimeImmutable $createdAt;

    public DateTimeImmutable $lastUpdateAt;

    public UserIdentifier $userIdentifier;

    public ?Checksum $settingsChecksum = null;

    public ?ShortIdentifier $settingsShortIdentifier = null;

    public ?ConsentSettingsId $settingsId = null;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
            'lastUpdateAt' => $this->lastUpdateAt->format(DateTimeInterface::ATOM),
            'userIdentifier' => $this->userIdentifier->value(),
            'settingsChecksum' => $this->settingsChecksum?->value(),
            'shortIdentifier' => $this->settingsShortIdentifier?->value(),
            'settingsId' => $this->settingsId?->toString(),
        ];
    }
}
