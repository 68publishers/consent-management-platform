<?php

declare(strict_types=1);

namespace App\Domain\Consent\Command;

use DateTimeImmutable;
use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class StoreConsentCommand extends AbstractCommand
{
    /**
     * @return static
     */
    public static function create(string $projectId, string $userIdentifier, ?string $settingsChecksum, array $consents, array $attributes): self
    {
        return self::fromParameters([
            'project_id' => $projectId,
            'user_identifier' => $userIdentifier,
            'settings_checksum' => $settingsChecksum,
            'consents' => $consents,
            'attributes' => $attributes,
        ]);
    }

    /**
     * @return $this
     */
    public function withCreatedAt(DateTimeImmutable $createdAt): self
    {
        return $this->withParam('created_at', $createdAt);
    }

    public function projectId(): string
    {
        return $this->getParam('project_id');
    }

    public function userIdentifier(): string
    {
        return $this->getParam('user_identifier');
    }

    public function settingsChecksum(): ?string
    {
        return $this->getParam('settings_checksum');
    }

    public function consents(): array
    {
        return $this->getParam('consents');
    }

    public function attributes(): array
    {
        return $this->getParam('attributes');
    }

    public function createdAt(): ?DateTimeImmutable
    {
        return $this->getParam('created_at');
    }
}
