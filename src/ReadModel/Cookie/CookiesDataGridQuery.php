<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

use App\ReadModel\AbstractDataGridQuery;

/**
 * Returns `array<CookieDataGridItemView>`
 */
final class CookiesDataGridQuery extends AbstractDataGridQuery
{
    public static function create(?string $locale): self
    {
        return self::fromParameters([
            'locale' => $locale,
        ]);
    }

    public function locale(): ?string
    {
        return $this->getParam('locale');
    }

    public function cookieProviderId(): ?string
    {
        return $this->getParam('cookie_provider_id');
    }

    public function projectId(): ?string
    {
        return $this->getParam('project_id');
    }

    public function projectServicesOnly(): bool
    {
        return $this->getParam('project_services_only') ?? false;
    }

    public function includeProjectsData(): bool
    {
        return $this->getParam('include_projects_data') ?? false;
    }

    public function withCookieProviderId(string $cookieProviderId): self
    {
        return $this->withParam('cookie_provider_id', $cookieProviderId);
    }

    public function withProjectId(string $projectId, bool $servicesOnly = false): self
    {
        return $this->withParam('project_id', $projectId)
            ->withParam('project_services_only', $servicesOnly);
    }

    public function withProjectsData(bool $includeProjectsData): self
    {
        return $this->withParam('include_projects_data', $includeProjectsData);
    }
}
