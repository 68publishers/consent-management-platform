<?php

declare(strict_types=1);

namespace App\Infrastructure\GlobalSettings\Doctrine\ReadModel;

use App\Domain\GlobalSettings\GlobalSettings;
use App\ReadModel\GlobalSettings\GetGlobalSettingsQuery;
use App\ReadModel\GlobalSettings\GlobalSettingsView;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;

final class GetGlobalSettingsQueryHandler implements QueryHandlerInterface
{
    private EntityManagerInterface $em;

    private ViewFactoryInterface $viewFactory;

    public function __construct(EntityManagerInterface $em, ViewFactoryInterface $viewFactory)
    {
        $this->em = $em;
        $this->viewFactory = $viewFactory;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(GetGlobalSettingsQuery $query): ?GlobalSettingsView
    {
        $data = $this->em->createQueryBuilder()
            ->select('gs')
            ->from(GlobalSettings::class, 'gs')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

        return null !== $data ? $this->viewFactory->create(GlobalSettingsView::class, DoctrineViewData::create($data)) : null;
    }
}
