<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class PutAzureAuthSettingsCommand extends AbstractCommand
{
    public static function create(bool $enabled, ?string $clientId, ?string $clientSecret): self
    {
        return self::fromParameters([
            'enabled' => $enabled,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);
    }

    public function enabled(): bool
    {
        return $this->getParam('enabled');
    }

    public function clientId(): ?string
    {
        return $this->getParam('client_id');
    }

    public function clientSecret(): ?string
    {
        return $this->getParam('client_secret');
    }
}
