<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Event;

use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\CookieProvider\ValueObject\Link;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieProviderLinkChanged extends AbstractDomainEvent
{
    private CookieProviderId $cookieProviderId;

    private Link $link;

    /**
     * @return static
     */
    public static function create(CookieProviderId $cookieProviderId, Link $link): self
    {
        $event = self::occur($cookieProviderId->toString(), [
            'link' => $link->value(),
        ]);

        $event->cookieProviderId = $cookieProviderId;
        $event->link = $link;

        return $event;
    }

    public function cookieProviderId(): CookieProviderId
    {
        return $this->cookieProviderId;
    }

    public function link(): Link
    {
        return $this->link;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->cookieProviderId = CookieProviderId::fromUuid($this->aggregateId()->id());
        $this->link = Link::fromValue($parameters['link']);
    }
}
