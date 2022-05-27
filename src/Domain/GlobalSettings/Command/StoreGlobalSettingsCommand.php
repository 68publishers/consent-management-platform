<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class StoreGlobalSettingsCommand extends AbstractCommand
{
	/**
	 * @return static
	 */
	public static function create(array $locales, string $defaultLocale): self
	{
		return self::fromParameters([
			'locales' => $locales,
			'default_locale' => $defaultLocale,
		]);
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
}
