<?php

declare(strict_types=1);

namespace App\ReadModel\ConsentSettings;

use App\Domain\ConsentSettings\ValueObject\ConsentSettingsId;
use App\Domain\ConsentSettings\ValueObject\SettingsGroup;
use App\Domain\ConsentSettings\ValueObject\ShortIdentifier;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Shared\ValueObject\Checksum;
use DateTimeImmutable;
use DateTimeInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class ConsentSettingsView extends AbstractView
{
    public ConsentSettingsId $id;

    public ProjectId $projectId;

    public DateTimeImmutable $createdAt;

    public DateTimeImmutable $lastUpdateAt;

    public Checksum $checksum;

    public SettingsGroup $settings;

    public ShortIdentifier $shortIdentifier;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'projectId' => $this->projectId->toString(),
            'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
            'lastUpdateAt' => $this->lastUpdateAt->format(DateTimeInterface::ATOM),
            'checksum' => $this->checksum->value(),
            'settings' => $this->settings->toArray(),
            'shortIdentifier' => $this->shortIdentifier->value(),
        ];
    }
}
