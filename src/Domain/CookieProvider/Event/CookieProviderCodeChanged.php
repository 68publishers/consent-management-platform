<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Event;

use App\Domain\CookieProvider\ValueObject\Code;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieProviderCodeChanged extends AbstractDomainEvent
{
    private CookieProviderId $cookieProviderId;

    private Code $code;

    /**
     * @return static
     */
    public static function create(CookieProviderId $cookieProviderId, Code $code): self
    {
        $event = self::occur($cookieProviderId->toString(), [
            'code' => $code->value(),
        ]);

        $event->cookieProviderId = $cookieProviderId;
        $event->code = $code;

        return $event;
    }

    public function cookieProviderId(): CookieProviderId
    {
        return $this->cookieProviderId;
    }

    public function code(): Code
    {
        return $this->code;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->cookieProviderId = CookieProviderId::fromUuid($this->aggregateId()->id());
        $this->code = Code::fromValue($parameters['code']);
    }
}
