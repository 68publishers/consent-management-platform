<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class StoreConsentSettingsCommand extends AbstractCommand
{
    public static function create(string $projectId, string $checksum, array $settings): self
    {
        return self::fromParameters([
            'project_id' => $projectId,
            'checksum' => $checksum,
            'settings' => $settings,
        ]);
    }

    public function projectId(): string
    {
        return $this->getParam('project_id');
    }

    public function checksum(): string
    {
        return $this->getParam('checksum');
    }

    public function setting(): array
    {
        return $this->getParam('settings');
    }
}
