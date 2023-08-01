<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CookieForm\Event;

use App\Domain\Cookie\ValueObject\CookieId;
use Symfony\Contracts\EventDispatcher\Event;

final class CookieUpdatedEvent extends Event
{
    public function __construct(
        private readonly CookieId $cookieId,
        private readonly string $oldName,
        private readonly string $newName,
    ) {}

    public function cookieId(): CookieId
    {
        return $this->cookieId;
    }

    public function oldName(): string
    {
        return $this->oldName;
    }

    public function newName(): string
    {
        return $this->newName;
    }
}
