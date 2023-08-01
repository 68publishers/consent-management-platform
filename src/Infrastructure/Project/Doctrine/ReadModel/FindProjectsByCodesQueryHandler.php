<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\ReadModel;

use App\Domain\Project\Project;
use App\ReadModel\Project\FindProjectsByCodesQuery;
use App\ReadModel\Project\ProjectView;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;

final class FindProjectsByCodesQueryHandler implements QueryHandlerInterface
{
    private EntityManagerInterface $em;

    private ViewFactoryInterface $viewFactory;

    public function __construct(EntityManagerInterface $em, ViewFactoryInterface $viewFactory)
    {
        $this->em = $em;
        $this->viewFactory = $viewFactory;
    }

    /**
     * @return array<ProjectView>
     */
    public function __invoke(FindProjectsByCodesQuery $query): array
    {
        $data = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Project::class, 'p')
            ->where('p.deletedAt IS NULL')
            ->andWhere('p.code IN (:codes)')
            ->setParameters([
                'codes' => $query->codes(),
            ])
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);

        return array_map(fn (array $row): ProjectView => $this->viewFactory->create(ProjectView::class, DoctrineViewData::create($row)), $data);
    }
}
