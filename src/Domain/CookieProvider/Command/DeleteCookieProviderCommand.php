<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class DeleteCookieProviderCommand extends AbstractCommand
{
    public static function create(string $cookieProviderId): self
    {
        return self::fromParameters([
            'cookie_provider_id' => $cookieProviderId,
        ]);
    }

    public function cookieProviderId(): string
    {
        return $this->getParam('cookie_provider_id');
    }
}
