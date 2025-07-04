<?php

declare(strict_types=1);

namespace App\Infrastructure\ConsentSettings\Doctrine\ReadModel;

use App\Domain\ConsentSettings\ConsentSettings;
use App\Domain\Project\Project;
use App\ReadModel\ConsentSettings\ConsentSettingsView;
use App\ReadModel\ConsentSettings\GetConsentSettingsByIdAndProjectIdQuery;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query')]
final readonly class GetConsentSettingByIdAndProjectIdQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private ViewFactoryInterface $viewFactory,
    ) {}

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(GetConsentSettingsByIdAndProjectIdQuery $query): ?ConsentSettingsView
    {
        $data = $this->em->createQueryBuilder()
            ->select('cs')
            ->from(ConsentSettings::class, 'cs')
            ->join(Project::class, 'p', Join::WITH, 'cs.projectId = p.id AND p.id = :projectId AND p.deletedAt IS NULL')
            ->where('cs.id = :id')
            ->setParameters([
                'projectId' => $query->projectId(),
                'id' => $query->id(),
            ])
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

        return null !== $data ? $this->viewFactory->create(ConsentSettingsView::class, DoctrineViewData::create($data)) : null;
    }
}
