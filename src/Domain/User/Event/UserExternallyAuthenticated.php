<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

use App\Domain\User\ValueObject\AuthProviderCode;
use App\Domain\User\ValueObject\AuthResourceOwnerId;
use App\Domain\User\ValueObject\AuthToken;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;

final class UserExternallyAuthenticated extends AbstractDomainEvent
{
    private UserId $userId;

    private AuthProviderCode $providerCode;

    private AuthResourceOwnerId $resourceOwnerId;

    private AuthToken $token;

    private AuthToken $refreshToken;

    public static function create(
        UserId $userId,
        AuthProviderCode $providerCode,
        AuthResourceOwnerId $resourceOwnerId,
        AuthToken $token,
        AuthToken $refreshToken,
    ): self {
        $event = self::occur($userId->toString(), [
            'provider_code' => $providerCode->value(),
            'resource_owner_id' => $resourceOwnerId->value(),
            'token' => $token->value(),
            'refresh_token' => $refreshToken->value(),
        ]);

        $event->userId = $userId;
        $event->providerCode = $providerCode;
        $event->resourceOwnerId = $resourceOwnerId;
        $event->token = $token;
        $event->refreshToken = $refreshToken;

        return $event;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function providerCode(): AuthProviderCode
    {
        return $this->providerCode;
    }

    public function resourceOwnerId(): AuthResourceOwnerId
    {
        return $this->resourceOwnerId;
    }

    public function token(): AuthToken
    {
        return $this->token;
    }

    public function refreshToken(): AuthToken
    {
        return $this->refreshToken;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->userId = UserId::fromUuid($this->aggregateId()->id());
        $this->providerCode = AuthProviderCode::fromValue($parameters['provider_code']);
        $this->resourceOwnerId = AuthResourceOwnerId::fromValue($parameters['resource_owner_id']);
        $this->token = AuthToken::fromValue($parameters['token']);
        $this->refreshToken = AuthToken::fromValue($parameters['refresh_token']);
    }
}
