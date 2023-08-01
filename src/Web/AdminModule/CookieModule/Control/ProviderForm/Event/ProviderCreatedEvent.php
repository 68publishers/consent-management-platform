<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\ProviderForm\Event;

use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use Symfony\Contracts\EventDispatcher\Event;

final class ProviderCreatedEvent extends Event
{
    private CookieProviderId $cookieProviderId;

    private string $code;

    public function __construct(CookieProviderId $cookieProviderId, string $code)
    {
        $this->cookieProviderId = $cookieProviderId;
        $this->code = $code;
    }

    public function cookieProviderId(): CookieProviderId
    {
        return $this->cookieProviderId;
    }

    public function code(): string
    {
        return $this->code;
    }
}
