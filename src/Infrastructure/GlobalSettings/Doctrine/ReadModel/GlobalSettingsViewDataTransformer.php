<?php

declare(strict_types=1);

namespace App\Infrastructure\GlobalSettings\Doctrine\ReadModel;

use App\Domain\Shared\ValueObject\LocalesConfig;
use App\ReadModel\GlobalSettings\GlobalSettingsView;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataTransformerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class GlobalSettingsViewDataTransformer implements ViewDataTransformerInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function canTransform(string $viewClassname, ViewDataInterface $viewData): bool
	{
		return is_a($viewClassname, GlobalSettingsView::class, TRUE) && $viewData instanceof DoctrineViewData;
	}

	/**
	 * {@inheritDoc}
	 */
	public function transform(ViewDataInterface $viewData): ViewDataInterface
	{
		return $viewData
			->with('locales', LocalesConfig::create($viewData->get('locales.locales'), $viewData->get('locales.defaultLocale')))
			->without('locales.locales', 'locales.defaultLocale');
	}
}
