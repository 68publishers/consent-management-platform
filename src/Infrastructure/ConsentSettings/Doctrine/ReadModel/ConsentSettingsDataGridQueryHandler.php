<?php

declare(strict_types=1);

namespace App\Infrastructure\ConsentSettings\Doctrine\ReadModel;

use App\Domain\ConsentSettings\ConsentSettings;
use App\Domain\Project\Project;
use App\Infrastructure\DataGridQueryHandlerTrait;
use App\ReadModel\ConsentSettings\ConsentSettingsDataGridQuery;
use App\ReadModel\ConsentSettings\ConsentSettingsView;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class ConsentSettingsDataGridQueryHandler implements QueryHandlerInterface
{
    use DataGridQueryHandlerTrait;

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function __invoke(ConsentSettingsDataGridQuery $query): array|int
    {
        return $this->processQuery(
            $query,
            function (ConsentSettingsDataGridQuery $query): QueryBuilder {
                return $this->em->createQueryBuilder()
                    ->select('COUNT(c.id)')
                    ->from(ConsentSettings::class, 'c')
                    ->join(Project::class, 'p', Join::WITH, 'c.projectId = p.id AND p.id = :projectId AND p.deletedAt IS NULL')
                    ->setParameter('projectId', $query->projectId());
            },
            function (ConsentSettingsDataGridQuery $query): QueryBuilder {
                return $this->em->createQueryBuilder()
                    ->select('c')
                    ->from(ConsentSettings::class, 'c')
                    ->join(Project::class, 'p', Join::WITH, 'c.projectId = p.id AND p.id = :projectId AND p.deletedAt IS NULL')
                    ->setParameter('projectId', $query->projectId());
            },
            ConsentSettingsView::class,
            [
                'checksum' => ['applyLike', 'c.checksum'],
                'shortIdentifier' => ['applyShortIdentifier', 'c.shortIdentifier'],
                'createdAt' => ['applyDate', 'c.createdAt'],
                'lastUpdateAt' => ['applyDate', 'c.lastUpdateAt'],
            ],
            [
                'checksum' => 'c.checksum',
                'shortIdentifier' => 'c.shortIdentifier',
                'createdAt' => 'c.createdAt',
                'lastUpdateAt' => 'c.lastUpdateAt',
            ],
        );
    }

    private function applyShortIdentifier(QueryBuilder $qb, string $column, mixed $value): void
    {
        $this->applyEquals($qb, $column, (int) $value);
    }
}
