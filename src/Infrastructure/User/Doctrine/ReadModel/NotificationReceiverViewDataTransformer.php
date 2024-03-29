<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Doctrine\ReadModel;

use App\Domain\Project\ValueObject\ProjectId;
use App\ReadModel\User\NotificationReceiverView;
use JsonException;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewDataTransformerInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\Name;

final class NotificationReceiverViewDataTransformer implements ViewDataTransformerInterface
{
    public function canTransform(string $viewClassname, ViewDataInterface $viewData): bool
    {
        return is_a($viewClassname, NotificationReceiverView::class, true) && $viewData instanceof DoctrineViewData;
    }

    /**
     * @throws JsonException
     */
    public function transform(ViewDataInterface $viewData, ViewFactoryInterface $viewFactory): ViewDataInterface
    {
        return $viewData
            ->with('name', Name::fromValues($viewData->get('name.firstname') ?? '', $viewData->get('name.surname') ?? ''))
            ->with('projectIds', array_map(static fn (string $id) => ProjectId::fromString($id), json_decode($viewData->get('projectIds'), true, 512, JSON_THROW_ON_ERROR)))
            ->without('name.firstname', 'name.surname');
    }
}
