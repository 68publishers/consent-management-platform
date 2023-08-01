<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CookieForm\Event;

use App\Domain\Cookie\ValueObject\CookieId;
use Symfony\Contracts\EventDispatcher\Event;

final class CookieCreatedEvent extends Event
{
    public function __construct(
        private readonly CookieId $cookieId,
        private readonly string $name,
    ) {}

    public function cookieId(): CookieId
    {
        return $this->cookieId;
    }

    public function name(): string
    {
        return $this->name;
    }
}
