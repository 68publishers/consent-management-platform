<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

use App\ReadModel\AbstractDataGridQuery;

/**
 * Returns CookieDataGridItemView[]
 */
final class CookiesDataGridQuery extends AbstractDataGridQuery
{
	/**
	 * @param string|NULL $locale
	 *
	 * @return static
	 */
	public static function create(?string $locale): self
	{
		return self::fromParameters([
			'locale' => $locale,
		]);
	}

	/**
	 * @return string|NULL
	 */
	public function locale(): ?string
	{
		return $this->getParam('locale');
	}

	/**
	 * @return string|NULL
	 */
	public function cookieProviderId(): ?string
	{
		return $this->getParam('cookie_provider_id');
	}

	/**
	 * @return string|NULL
	 */
	public function projectId(): ?string
	{
		return $this->getParam('project_id');
	}

	/**
	 * @return bool
	 */
	public function projectServicesOnly(): bool
	{
		return $this->getParam('project_services_only') ?? FALSE;
	}

	/**
	 * @return bool
	 */
	public function includeProjectsData(): bool
	{
		return $this->getParam('include_projects_data') ?? FALSE;
	}

	/**
	 * @param string $cookieProviderId
	 *
	 * @return $this
	 */
	public function withCookieProviderId(string $cookieProviderId): self
	{
		return $this->withParam('cookie_provider_id', $cookieProviderId);
	}

	/**
	 * @param string $projectId
	 * @param bool   $servicesOnly
	 *
	 * @return $this
	 */
	public function withProjectId(string $projectId, bool $servicesOnly = FALSE): self
	{
		return $this->withParam('project_id', $projectId)
			->withParam('project_services_only', $servicesOnly);
	}

	/**
	 * @param bool $includeProjectsData
	 *
	 * @return $this
	 */
	public function withProjectsData(bool $includeProjectsData): self
	{
		return $this->withParam('include_projects_data', $includeProjectsData);
	}
}
