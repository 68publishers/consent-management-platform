<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User\ValueObject\AuthProviderCode;
use App\Domain\User\ValueObject\AuthResourceOwnerId;
use App\Domain\User\ValueObject\AuthToken;
use DateTimeImmutable;

final class ExternalAuth
{
    public function __construct(
        private readonly User $user,
        private readonly AuthProviderCode $providerCode,
        private readonly DateTimeImmutable $createdAt,
        private AuthResourceOwnerId $resourceOwnerId,
        private AuthToken $token,
        private AuthToken $refreshToken,
    ) {}

    public function getUser(): User
    {
        return $this->user;
    }

    public function getProviderCode(): AuthProviderCode
    {
        return $this->providerCode;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getResourceOwnerId(): AuthResourceOwnerId
    {
        return $this->resourceOwnerId;
    }

    public function getToken(): AuthToken
    {
        return $this->token;
    }

    public function getRefreshToken(): AuthToken
    {
        return $this->refreshToken;
    }

    public function updateTokens(AuthResourceOwnerId $resourceOwnerId, AuthToken $token, AuthToken $refreshToken): void
    {
        if (!$this->resourceOwnerId->equals($resourceOwnerId)) {
            $this->resourceOwnerId = $resourceOwnerId;
        }

        if (!$this->token->equals($token)) {
            $this->token = $token;
        }

        if (!$this->refreshToken->equals($refreshToken)) {
            $this->refreshToken = $refreshToken;
        }
    }
}
