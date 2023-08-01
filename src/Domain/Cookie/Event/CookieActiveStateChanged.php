<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Event;

use App\Domain\Cookie\ValueObject\CookieId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieActiveStateChanged extends AbstractDomainEvent
{
    private CookieId $cookieId;

    private bool $active;

    public static function create(CookieId $cookieId, bool $active): self
    {
        $event = self::occur($cookieId->toString(), [
            'active' => $active,
        ]);

        $event->cookieId = $cookieId;
        $event->active = $active;

        return $event;
    }

    public function cookieId(): CookieId
    {
        return $this->cookieId;
    }

    public function active(): bool
    {
        return $this->active;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->cookieId = CookieId::fromUuid($this->aggregateId()->id());
        $this->active = (bool) $parameters['active'];
    }
}
