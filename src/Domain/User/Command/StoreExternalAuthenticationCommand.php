<?php

declare(strict_types=1);

namespace App\Domain\User\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class StoreExternalAuthenticationCommand extends AbstractCommand
{
    /**
     * @param array<int, string> $roles
     */
    public static function create(
        string $userId,
        string $providerCode,
        string $resourceOwnerId,
        string $token,
        string $refreshToken,
        array $roles,
    ): self {
        return self::fromParameters([
            'user_id' => $userId,
            'provider_code' => $providerCode,
            'resource_owner_id' => $resourceOwnerId,
            'token' => $token,
            'refresh_token' => $refreshToken,
            'roles' => $roles,
        ]);
    }

    public function userId(): string
    {
        return $this->getParam('user_id');
    }

    public function providerCode(): string
    {
        return $this->getParam('provider_code');
    }

    public function resourceOwnerId(): string
    {
        return $this->getParam('resource_owner_id');
    }

    public function token(): string
    {
        return $this->getParam('token');
    }

    public function refreshToken(): string
    {
        return $this->getParam('refresh_token');
    }

    public function roles(): array
    {
        return $this->getParam('roles');
    }
}
