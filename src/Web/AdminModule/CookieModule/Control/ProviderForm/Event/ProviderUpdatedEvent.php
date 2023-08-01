<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\ProviderForm\Event;

use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use Symfony\Contracts\EventDispatcher\Event;

final class ProviderUpdatedEvent extends Event
{
    public function __construct(
        private readonly CookieProviderId $cookieProviderId,
        private readonly string $oldCode,
        private readonly string $newCode,
    ) {}

    public function cookieProviderId(): CookieProviderId
    {
        return $this->cookieProviderId;
    }

    public function oldCode(): string
    {
        return $this->oldCode;
    }

    public function newCode(): string
    {
        return $this->newCode;
    }
}
