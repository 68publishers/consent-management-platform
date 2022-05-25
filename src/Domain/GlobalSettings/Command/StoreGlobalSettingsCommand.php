<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class StoreGlobalSettingsCommand extends AbstractCommand
{
	/**
	 * @return static
	 */
	public static function create(array $locales): self
	{
		return self::fromParameters([
			'locales' => $locales,
		]);
	}

	/**
	 * @return array
	 */
	public function locales(): array
	{
		return $this->getParam('locales');
	}
}
