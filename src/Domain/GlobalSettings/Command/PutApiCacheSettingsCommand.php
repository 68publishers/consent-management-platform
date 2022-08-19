<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class PutApiCacheSettingsCommand extends AbstractCommand
{
	/**
	 * @return static
	 */
	public static function create(array $cacheControlDirectives, bool $useEntityTag): self
	{
		return self::fromParameters([
			'cache_control_directives' => $cacheControlDirectives,
			'use_entity_tag' => $useEntityTag,
		]);
	}

	/**
	 * @return array
	 */
	public function cacheControlDirectives(): array
	{
		return $this->getParam('cache_control_directives');
	}

	/**
	 * @return bool
	 */
	public function useEntityTag(): bool
	{
		return $this->getParam('use_entity_tag');
	}
}
