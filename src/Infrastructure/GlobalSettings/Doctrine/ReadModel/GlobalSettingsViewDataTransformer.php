<?php

declare(strict_types=1);

namespace App\Infrastructure\GlobalSettings\Doctrine\ReadModel;

use App\Domain\GlobalSettings\ValueObject\ApiCache;
use App\Domain\Shared\ValueObject\LocalesConfig;
use App\ReadModel\GlobalSettings\GlobalSettingsView;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataTransformerInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;

final class GlobalSettingsViewDataTransformer implements ViewDataTransformerInterface
{
    public function canTransform(string $viewClassname, ViewDataInterface $viewData): bool
    {
        return is_a($viewClassname, GlobalSettingsView::class, true) && $viewData instanceof DoctrineViewData;
    }

    public function transform(ViewDataInterface $viewData, ViewFactoryInterface $viewFactory): ViewDataInterface
    {
        return $viewData
            ->with('locales', LocalesConfig::create($viewData->get('locales.locales'), $viewData->get('locales.defaultLocale')))
            ->with('apiCache', ApiCache::create($viewData->get('apiCache.cacheControlDirectives'), $viewData->get('apiCache.useEntityTag')))
            ->without('locales.locales', 'locales.defaultLocale', 'apiCache.cacheControlDirectives', 'apiCache.useEntityTag');
    }
}
