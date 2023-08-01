<?php

declare(strict_types=1);

namespace App\Infrastructure\Import\Doctrine\ReadModel;

use App\ReadModel\Import\ImportListView;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataTransformerInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\Name;

final class ImportListViewDataTransformer implements ViewDataTransformerInterface
{
    public function canTransform(string $viewClassname, ViewDataInterface $viewData): bool
    {
        return is_a($viewClassname, ImportListView::class, true) && $viewData instanceof DoctrineViewData;
    }

    public function transform(ViewDataInterface $viewData, ViewFactoryInterface $viewFactory): ViewDataInterface
    {
        if (null !== $viewData->get('name.firstname') && null !== $viewData->get('name.surname')) {
            $viewData = $viewData
                ->with('authorName', Name::fromValues($viewData->get('name.firstname'), $viewData->get('name.surname')))
                ->without('name.firstname', 'name.surname');
        }

        return $viewData;
    }
}
