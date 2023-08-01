<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\ProviderForm\Event;

use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use Symfony\Contracts\EventDispatcher\Event;

final class ProviderCreatedEvent extends Event
{
    public function __construct(
        private readonly CookieProviderId $cookieProviderId,
        private readonly string $code,
    ) {}

    public function cookieProviderId(): CookieProviderId
    {
        return $this->cookieProviderId;
    }

    public function code(): string
    {
        return $this->code;
    }
}
