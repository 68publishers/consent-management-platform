<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Event;

use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Cookie\ValueObject\Name;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieNameChanged extends AbstractDomainEvent
{
    private CookieId $cookieId;

    private Name $name;

    public static function create(CookieId $cookieId, Name $name): self
    {
        $event = self::occur($cookieId->toString(), [
            'name' => $name->value(),
        ]);

        $event->cookieId = $cookieId;
        $event->name = $name;

        return $event;
    }

    public function cookieId(): CookieId
    {
        return $this->cookieId;
    }

    public function name(): Name
    {
        return $this->name;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->cookieId = CookieId::fromUuid($this->aggregateId()->id());
        $this->name = Name::fromValue($parameters['name']);
    }
}
