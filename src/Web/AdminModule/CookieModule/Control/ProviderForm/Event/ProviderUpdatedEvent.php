<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\ProviderForm\Event;

use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use Symfony\Contracts\EventDispatcher\Event;

final class ProviderUpdatedEvent extends Event
{
    private CookieProviderId $cookieProviderId;

    private string $oldCode;

    private string $newCode;

    public function __construct(CookieProviderId $cookieProviderId, string $oldCode, string $newCode)
    {
        $this->cookieProviderId = $cookieProviderId;
        $this->oldCode = $oldCode;
        $this->newCode = $newCode;
    }

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
