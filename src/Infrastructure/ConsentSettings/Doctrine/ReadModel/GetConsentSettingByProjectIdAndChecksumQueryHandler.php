<?php

declare(strict_types=1);

namespace App\Infrastructure\ConsentSettings\Doctrine\ReadModel;

use App\Domain\ConsentSettings\ConsentSettings;
use App\Domain\Project\Project;
use App\ReadModel\ConsentSettings\ConsentSettingsView;
use App\ReadModel\ConsentSettings\GetConsentSettingsByProjectIdAndChecksumQuery;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;

final class GetConsentSettingByProjectIdAndChecksumQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ViewFactoryInterface $viewFactory,
    ) {}

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(GetConsentSettingsByProjectIdAndChecksumQuery $query): ?ConsentSettingsView
    {
        $data = $this->em->createQueryBuilder()
            ->select('cs')
            ->from(ConsentSettings::class, 'cs')
            ->join(Project::class, 'p', Join::WITH, 'cs.projectId = p.id AND p.id = :projectId AND p.deletedAt IS NULL')
            ->where('cs.checksum = :checksum')
            ->setParameters([
                'projectId' => $query->projectId(),
                'checksum' => $query->checksum(),
            ])
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

        return null !== $data ? $this->viewFactory->create(ConsentSettingsView::class, DoctrineViewData::create($data)) : null;
    }
}
