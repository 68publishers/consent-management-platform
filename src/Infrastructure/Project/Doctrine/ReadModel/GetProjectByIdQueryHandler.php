<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\ReadModel;

use App\Domain\Project\Project;
use App\ReadModel\Project\GetProjectByIdQuery;
use App\ReadModel\Project\ProjectView;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query')]
final readonly class GetProjectByIdQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private ViewFactoryInterface $viewFactory,
    ) {}

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(GetProjectByIdQuery $query): ?ProjectView
    {
        $data = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Project::class, 'p')
            ->where('p.id = :id')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('id', $query->id())
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

        return null !== $data ? $this->viewFactory->create(ProjectView::class, DoctrineViewData::create($data)) : null;
    }
}
