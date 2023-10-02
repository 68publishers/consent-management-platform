<?php

declare(strict_types=1);

namespace App\Domain\Project\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class CreateProjectCommand extends AbstractCommand
{
    /**
     * @param array<int, string> $locales
     * @param array<int, string> $environments
     */
    public static function create(
        string $name,
        string $code,
        string $domain,
        string $description,
        string $color,
        bool $active,
        array $locales,
        string $defaultLocale,
        array $environments,
        ?string $projectId = null,
        ?string $cookieProviderId = null,
    ): self {
        return self::fromParameters([
            'name' => $name,
            'code' => $code,
            'domain' => $domain,
            'description' => $description,
            'color' => $color,
            'active' => $active,
            'locales' => $locales,
            'default_locale' => $defaultLocale,
            'environments' => $environments,
            'project_id' => $projectId,
            'cookie_provider_id' => $cookieProviderId,
            'cookie_provider_ids' => [],
        ]);
    }

    /**
     * @param array<string> $cookieProviderIds
     */
    public function withCookieProviderIds(array $cookieProviderIds): self
    {
        return $this->withParam('cookie_provider_ids', $cookieProviderIds);
    }

    public function name(): string
    {
        return $this->getParam('name');
    }

    public function code(): string
    {
        return $this->getParam('code');
    }

    public function domain(): string
    {
        return $this->getParam('domain');
    }

    public function description(): string
    {
        return $this->getParam('description');
    }

    public function color(): string
    {
        return $this->getParam('color');
    }

    public function active(): bool
    {
        return $this->getParam('active');
    }

    /**
     * @return array<int, string>
     */
    public function locales(): array
    {
        return $this->getParam('locales');
    }

    public function defaultLocale(): string
    {
        return $this->getParam('default_locale');
    }

    /**
     * @return array<int, string>
     */
    public function environments(): array
    {
        return $this->getParam('environments');
    }

    /**
     * @return array<string>
     */
    public function cookieProviderIds(): array
    {
        return $this->getParam('cookie_provider_ids');
    }

    public function projectId(): ?string
    {
        return $this->getParam('project_id');
    }

    public function cookieProviderId(): ?string
    {
        return $this->getParam('cookie_provider_id');
    }
}
