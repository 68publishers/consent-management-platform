<?php

declare(strict_types=1);

namespace App\Domain\Project\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class UpdateProjectCommand extends AbstractCommand
{
    public static function create(string $projectId): self
    {
        return self::fromParameters([
            'project_id' => $projectId,
        ]);
    }

    public function projectId(): string
    {
        return $this->getParam('project_id');
    }

    public function name(): ?string
    {
        return $this->getParam('name');
    }

    public function code(): ?string
    {
        return $this->getParam('code');
    }

    public function domain(): ?string
    {
        return $this->getParam('domain');
    }

    public function description(): ?string
    {
        return $this->getParam('description');
    }

    public function color(): ?string
    {
        return $this->getParam('color');
    }

    public function active(): ?bool
    {
        return $this->getParam('active');
    }

    /**
     * @return array<int, string>|null
     */
    public function locales(): ?array
    {
        return $this->getParam('locales');
    }

    public function defaultLocale(): ?string
    {
        return $this->getParam('default_locale');
    }

    /**
     * @return array<int, string>|null
     */
    public function environments(): ?array
    {
        return $this->getParam('environments');
    }

    /**
     * @return array<string>|null
     */
    public function cookieProviderIds(): ?array
    {
        return $this->getParam('cookie_provider_ids');
    }

    public function withName(string $name): self
    {
        return $this->withParam('name', $name);
    }

    public function withCode(string $code): self
    {
        return $this->withParam('code', $code);
    }

    public function withDomain(string $domain): self
    {
        return $this->withParam('domain', $domain);
    }

    public function withColor(string $color): self
    {
        return $this->withParam('color', $color);
    }

    public function withDescription(string $description): self
    {
        return $this->withParam('description', $description);
    }

    public function withActive(bool $active): self
    {
        return $this->withParam('active', $active);
    }

    /**
     * @param array<int, string> $locales
     */
    public function withLocales(array $locales, string $defaultLocale): self
    {
        return $this->withParam('locales', $locales)
            ->withParam('default_locale', $defaultLocale);
    }

    /**
     * @param array<string> $cookieProviderIds
     */
    public function withCookieProviderIds(array $cookieProviderIds): self
    {
        return $this->withParam('cookie_provider_ids', $cookieProviderIds);
    }

    /**
     * @param array<int, string> $environments
     */
    public function withEnvironments(array $environments): self
    {
        return $this->withParam('environments', $environments);
    }
}
