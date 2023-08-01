<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class DeleteCookieCommand extends AbstractCommand
{
    public static function create(string $cookieId): self
    {
        return self::fromParameters([
            'cookie_id' => $cookieId,
        ]);
    }

    public function cookieId(): string
    {
        return $this->getParam('cookie_id');
    }
}
