<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\QueryHandler;

use App\ReadModel\Project\ProjectView;
use App\Domain\Shared\ValueObject\LocalesConfig;

final class ViewFactory
{
	private function __construct()
	{
	}

	/**
	 * @param array $data
	 *
	 * @return \App\ReadModel\Project\ProjectView
	 */
	public static function createProjectView(array $data): ProjectView
	{
		$data['locales'] = LocalesConfig::create($data['locales.locales'], $data['locales.defaultLocale']);
		unset($data['locales.locales'], $data['locales.defaultLocale']);

		return ProjectView::fromArray($data);
	}
}
