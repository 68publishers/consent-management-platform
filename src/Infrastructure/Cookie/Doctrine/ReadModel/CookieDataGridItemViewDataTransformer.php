<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie\Doctrine\ReadModel;

use App\ReadModel\Cookie\CookieDataGridItemView;
use JsonException;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataTransformerInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;

final class CookieDataGridItemViewDataTransformer implements ViewDataTransformerInterface
{
    public function canTransform(string $viewClassname, ViewDataInterface $viewData): bool
    {
        return is_a($viewClassname, CookieDataGridItemView::class, true) && $viewData instanceof DoctrineViewData;
    }

    /**
     * @throws JsonException
     */
    public function transform(ViewDataInterface $viewData, ViewFactoryInterface $viewFactory): ViewDataInterface
    {
        $projects = $viewData->get('projects');

        if (is_string($projects)) {
            $projects = json_decode($projects, true, 512, JSON_THROW_ON_ERROR);
        }

        return $viewData->with('projects', $projects);
    }
}
