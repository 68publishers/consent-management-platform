<?php

declare(strict_types=1);

namespace App\Domain\Project\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class CreateProjectCommand extends AbstractCommand
{
	/**
	 * @param string      $name
	 * @param string      $code
	 * @param string      $description
	 * @param string      $color
	 * @param bool        $active
	 * @param array       $locales
	 * @param string      $defaultLocale
	 * @param string|NULL $projectId
	 * @param string|NULL $cookieProviderId
	 *
	 * @return static
	 */
	public static function create(string $name, string $code, string $description, string $color, bool $active, array $locales, string $defaultLocale, ?string $projectId = NULL, ?string $cookieProviderId = NULL): self
	{
		return self::fromParameters([
			'name' => $name,
			'code' => $code,
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
	 * @param string[] $cookieProviderIds
	 *
	 * @return $this
	 */
	public function withCookieProviderIds(array $cookieProviderIds): self
	{
		return $this->withParam('cookie_provider_ids', $cookieProviderIds);
	}

	/**
	 * @return string
	 */
	public function name(): string
	{
		return $this->getParam('name');
	}

	/**
	 * @return string
	 */
	public function code(): string
	{
		return $this->getParam('code');
	}

	/**
	 * @return string
	 */
	public function description(): string
	{
		return $this->getParam('description');
	}

	/**
	 * @return string
	 */
	public function color(): string
	{
		return $this->getParam('color');
	}

	/**
	 * @return bool
	 */
	public function active(): bool
	{
		return $this->getParam('active');
	}

	/**
	 * @return array
	 */
	public function locales(): array
	{
		return $this->getParam('locales');
	}

	/**
	 * @return string
	 */
	public function defaultLocale(): string
	{
		return $this->getParam('default_locale');
	}

	/**
	 * @return string[]
	 */
	public function cookieProviderIds(): array
	{
		return $this->getParam('cookie_provider_ids');
	}

	/**
	 * @return string|NULL
	 */
	public function projectId(): ?string
	{
		return $this->getParam('project_id');
	}

	/**
	 * @return string|NULL
	 */
	public function cookieProviderId(): ?string
	{
		return $this->getParam('cookie_provider_id');
	}
}
