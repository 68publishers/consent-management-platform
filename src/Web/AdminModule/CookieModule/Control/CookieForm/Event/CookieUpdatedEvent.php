<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CookieForm\Event;

use App\Domain\Cookie\ValueObject\CookieId;
use Symfony\Contracts\EventDispatcher\Event;

final class CookieUpdatedEvent extends Event
{
    private CookieId $cookieId;

    private string $oldName;

    private string $newName;

    public function __construct(CookieId $cookieId, string $oldName, string $newName)
    {
        $this->cookieId = $cookieId;
        $this->oldName = $oldName;
        $this->newName = $newName;
    }

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
