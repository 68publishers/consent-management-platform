<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Event;

use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Cookie\ValueObject\Purpose;
use App\Domain\Shared\ValueObject\Locale;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookiePurposeChanged extends AbstractDomainEvent
{
    private CookieId $cookieId;

    private Locale $locale;

    private Purpose $purpose;

    public static function create(CookieId $cookieId, Locale $locale, Purpose $purpose): self
    {
        $event = self::occur($cookieId->toString(), [
            'locale' => $locale->value(),
            'purpose' => $purpose->value(),
        ]);

        $event->cookieId = $cookieId;
        $event->locale = $locale;
        $event->purpose = $purpose;

        return $event;
    }

    public function cookieId(): CookieId
    {
        return $this->cookieId;
    }

    public function locale(): Locale
    {
        return $this->locale;
    }

    public function purpose(): Purpose
    {
        return $this->purpose;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->cookieId = CookieId::fromUuid($this->aggregateId()->id());
        $this->locale = Locale::fromValue($parameters['locale']);
        $this->purpose = Purpose::fromValue($parameters['purpose']);
    }
}
