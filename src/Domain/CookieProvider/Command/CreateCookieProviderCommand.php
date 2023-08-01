<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class CreateCookieProviderCommand extends AbstractCommand
{
    /**
     * @param array<string> $purposes
     */
    public static function create(string $code, string $type, string $name, string $link, array $purposes, bool $private, bool $active, ?string $cookieProviderId = null): self
    {
        return self::fromParameters([
            'code' => $code,
            'type' => $type,
            'name' => $name,
            'link' => $link,
            'purposes' => $purposes,
            'private' => $private,
            'active' => $active,
            'cookie_provider_id' => $cookieProviderId,
        ]);
    }

    public function code(): string
    {
        return $this->getParam('code');
    }

    public function type(): string
    {
        return $this->getParam('type');
    }

    public function name(): string
    {
        return $this->getParam('name');
    }

    public function link(): string
    {
        return $this->getParam('link');
    }

    /**
     * @return array<string>
     */
    public function purposes(): array
    {
        return $this->getParam('purposes');
    }

    public function private(): bool
    {
        return $this->getParam('private');
    }

    public function active(): bool
    {
        return $this->getParam('active');
    }

    public function cookieProviderId(): ?string
    {
        return $this->getParam('cookie_provider_id');
    }
}
