<?php

declare(strict_types=1);

namespace App\Domain\Project\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class CreateProjectCommand extends AbstractCommand
{
	/**
	 * @param array<int, string> $locales
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
		?string $projectId = NULL,
		?string $cookieProviderId = NULL
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

	public function locales(): array
	{
		return $this->getParam('locales');
	}

	public function defaultLocale(): string
	{
		return $this->getParam('default_locale');
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
