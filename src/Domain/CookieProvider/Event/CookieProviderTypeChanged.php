<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Event;

use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieProviderTypeChanged extends AbstractDomainEvent
{
    private CookieProviderId $cookieProviderId;

    private ProviderType $type;

    public static function create(CookieProviderId $cookieProviderId, ProviderType $type): self
    {
        $event = self::occur($cookieProviderId->toString(), [
            'type' => $type->value(),
        ]);

        $event->cookieProviderId = $cookieProviderId;
        $event->type = $type;

        return $event;
    }

    public function cookieProviderId(): CookieProviderId
    {
        return $this->cookieProviderId;
    }

    public function type(): ProviderType
    {
        return $this->type;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->cookieProviderId = CookieProviderId::fromUuid($this->aggregateId()->id());
        $this->type = ProviderType::fromValue($parameters['type']);
    }
}
