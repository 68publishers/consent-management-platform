<?php

declare(strict_types=1);

namespace App\Domain\Project\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class AddCookieProvidersToProjectCommand extends AbstractCommand
{
	/**
	 * @param string $projectId
	 * @param string $cookieProviderId
	 * @param string ...$cookieProviderIds
	 *
	 * @return static
	 */
	public static function create(string $projectId, string $cookieProviderId, string ...$cookieProviderIds): self
	{
		return self::fromParameters([
			'project_id' => $projectId,
			'cookie_provider_ids' => array_merge([$cookieProviderId], $cookieProviderIds),
		]);
	}

	/**
	 * @return string
	 */
	public function projectId(): string
	{
		return $this->getParam('project_id');
	}

	/**
	 * @return string[]
	 */
	public function cookieProviderIds(): array
	{
		return $this->getParam('cookie_provider_ids');
	}
}
