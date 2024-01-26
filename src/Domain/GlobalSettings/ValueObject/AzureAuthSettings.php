<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\ValueObject;

use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractArrayValueObject;

final class AzureAuthSettings extends AbstractArrayValueObject
{
    public static function fromValues(
        bool $enabled,
        ?string $clientId,
        ?string $clientSecret,
    ): self {
        return self::fromArray([
            'enabled' => $enabled,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ]);
    }

    public function enabled(): bool
    {
        return $this->get('enabled') ?? false;
    }

    public function clientId(): ?string
    {
        return $this->get('client_id');
    }

    public function clientSecret(): ?string
    {
        return $this->get('client_secret');
    }
}
