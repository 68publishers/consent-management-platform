<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\ReadModel;

use App\Domain\Shared\ValueObject\LocalesConfig;
use App\ReadModel\Project\ProjectView;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataTransformerInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;

final class ProjectViewDataTransformer implements ViewDataTransformerInterface
{
    public function canTransform(string $viewClassname, ViewDataInterface $viewData): bool
    {
        return is_a($viewClassname, ProjectView::class, true) && $viewData instanceof DoctrineViewData;
    }

    public function transform(ViewDataInterface $viewData, ViewFactoryInterface $viewFactory): ViewDataInterface
    {
        return $viewData
            ->with('locales', LocalesConfig::create($viewData->get('locales.locales'), $viewData->get('locales.defaultLocale')))
            ->without('locales.locales', 'locales.defaultLocale');
    }
}
