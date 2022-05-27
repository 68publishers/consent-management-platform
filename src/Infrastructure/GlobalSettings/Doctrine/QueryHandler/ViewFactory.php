<?php

declare(strict_types=1);

namespace App\Infrastructure\GlobalSettings\Doctrine\QueryHandler;

use App\Domain\Shared\ValueObject\LocalesConfig;
use App\ReadModel\GlobalSettings\GlobalSettingsView;

final class ViewFactory
{
	private function __construct()
	{
	}

	/**
	 * @param array $data
	 *
	 * @return \App\ReadModel\GlobalSettings\GlobalSettingsView
	 */
	public static function createGlobalSettingsView(array $data): GlobalSettingsView
	{
		$data['locales'] = LocalesConfig::create($data['locales.locales'], $data['locales.defaultLocale']);
		unset($data['locales.locales'], $data['locales.defaultLocale']);

		return GlobalSettingsView::fromArray($data);
	}
}
