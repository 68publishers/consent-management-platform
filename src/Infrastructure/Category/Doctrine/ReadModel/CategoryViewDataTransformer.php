<?php

declare(strict_types=1);

namespace App\Infrastructure\Category\Doctrine\ReadModel;

use App\ReadModel\Category\CategoryView;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataTransformerInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;

final class CategoryViewDataTransformer implements ViewDataTransformerInterface
{
    public function canTransform(string $viewClassname, ViewDataInterface $viewData): bool
    {
        return is_a($viewClassname, CategoryView::class, true) && $viewData instanceof DoctrineViewData;
    }

    public function transform(ViewDataInterface $viewData, ViewFactoryInterface $viewFactory): ViewDataInterface
    {
        $names = [];

        if ($viewData->has('translations')) {
            foreach ($viewData->get('translations') as $translation) {
                $names[$translation['locale']->value()] = $translation['name'];
            }

            $viewData = $viewData->without('translations');
        }

        return $viewData->with('names', $names);
    }
}
