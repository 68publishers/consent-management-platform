<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\ReadModel;

use App\Domain\Project\Project;
use App\Domain\User\UserHasProject;
use App\ReadModel\Project\FindProjectSelectOptionsQuery;
use App\ReadModel\Project\ProjectSelectOptionView;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;

final class FindProjectSelectOptionsQueryHandler implements QueryHandlerInterface
{
    private EntityManagerInterface $em;

    private ViewFactoryInterface $viewFactory;

    public function __construct(EntityManagerInterface $em, ViewFactoryInterface $viewFactory)
    {
        $this->em = $em;
        $this->viewFactory = $viewFactory;
    }

    /**
     * @return ProjectSelectOptionView[]
     */
    public function __invoke(FindProjectSelectOptionsQuery $query): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('p.id, p.name')
            ->from(Project::class, 'p')
            ->where('p.deletedAt IS NULL')
            ->orderBy('p.name', 'ASC');

        if (null !== $query->userId()) {
            $qb->join(UserHasProject::class, 'uhp', Join::WITH, 'uhp.projectId = p.id AND uhp.user = :userId')
                ->setParameter('userId', $query->userId());
        }

        if (null !== $query->cookieProviderId()) {
            $qb->join('p.cookieProviders', 'phc', Join::WITH, 'phc.cookieProviderId = :cookieProviderId')
                ->setParameter('cookieProviderId', $query->cookieProviderId());
        }

        $data = $qb->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);

        return array_map(fn (array $row): ProjectSelectOptionView => $this->viewFactory->create(ProjectSelectOptionView::class, DoctrineViewData::create($row)), $data);
    }
}
