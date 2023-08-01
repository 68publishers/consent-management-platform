<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Event;

use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\CookieProvider\ValueObject\Name;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieProviderNameChanged extends AbstractDomainEvent
{
    private CookieProviderId $cookieProviderId;

    private Name $name;

    public static function create(CookieProviderId $cookieProviderId, Name $name): self
    {
        $event = self::occur($cookieProviderId->toString(), [
            'name' => $name->value(),
        ]);

        $event->cookieProviderId = $cookieProviderId;
        $event->name = $name;

        return $event;
    }

    public function cookieProviderId(): CookieProviderId
    {
        return $this->cookieProviderId;
    }

    public function name(): Name
    {
        return $this->name;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->cookieProviderId = CookieProviderId::fromUuid($this->aggregateId()->id());
        $this->name = Name::fromValue($parameters['name']);
    }
}
