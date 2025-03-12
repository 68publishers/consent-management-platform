<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent\Doctrine\ReadModel;

use App\Infrastructure\DataGridQueryHandlerTrait;
use App\ReadModel\Consent\ConsentListView;
use App\ReadModel\Consent\ConsentsDataGridQuery;
use App\ReadModel\DataGridQueryInterface;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder as DbalQueryBuilder;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder as OrmQueryBuilder;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class ConsentsDataGridQueryHandler implements QueryHandlerInterface
{
    use DataGridQueryHandlerTrait;

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function __invoke(ConsentsDataGridQuery $query): array|int
    {
        return $this->processQuery(
            $query,
            function () use ($query): DbalQueryBuilder|string {
                if ($query->isCountEstimateOnly()) {
                    return self::EstimateOnly;
                }

                return $this->em->getConnection()->createQueryBuilder()
                    ->select('1')
                    ->from('consent', 'c')
                    ->andWhere('c.project_id = :projectId')
                    ->setMaxResults(ConsentsDataGridQuery::CountLimit)
                    ->setParameter('projectId', $query->projectId());
            },
            function () use ($query): DbalQueryBuilder {
                return $this->em->getConnection()->createQueryBuilder()
                    ->select('c.id, c.created_at, c.last_update_at, c.user_identifier, c.settings_checksum, c.environment, cs.short_identifier AS settings_short_identifier, cs.id AS settings_id')
                    ->from('consent', 'c')
                    ->leftJoin('c', 'consent_settings', 'cs', 'cs.project_id = c.project_id AND cs.checksum = c.settings_checksum')
                    ->andWhere('c.project_id = :projectId')
                    ->setParameter('projectId', $query->projectId(), Types::GUID);
            },
            fn (array $row): ConsentListView => new ConsentListView(
                id: $row['id'],
                createdAt: $this->em->getConnection()->convertToPHPValue($row['created_at'], Types::DATETIME_IMMUTABLE),
                lastUpdateAt: $this->em->getConnection()->convertToPHPValue($row['last_update_at'], Types::DATETIME_IMMUTABLE),
                userIdentifier: $row['user_identifier'],
                environment: $row['environment'],
                settingsChecksum: $row['settings_checksum'],
                settingsShortIdentifier: $row['settings_short_identifier'],
                settingsId: $row['settings_id'],
            ),
            [
                'userIdentifier' => ['applyEquals', 'c.user_identifier'],
                'createdAt' => ['applyDate', 'c.created_at'],
                'lastUpdateAt' => ['applyDate', 'c.last_update_at'],
                'environment' => ['applyIn', 'c.environment'],
            ],
            [
                'userIdentifier' => 'c.user_identifier',
                'createdAt' => 'c.created_at',
                'lastUpdateAt' => 'c.last_update_at',
            ],
        );
    }

    protected function beforeCountQueryFetch(OrmQueryBuilder|DbalQueryBuilder $qb, DataGridQueryInterface $query): DbalQueryBuilder
    {
        assert($qb instanceof DbalQueryBuilder && $query instanceof ConsentsDataGridQuery);

        if ($query->isCountEstimateOnly()) {
            return $qb;
        }

        return $this->em->getConnection()->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('(' . $qb->getSQL() . ')', 't')
            ->setParameters($qb->getParameters(), $qb->getParameterTypes());
    }
}
