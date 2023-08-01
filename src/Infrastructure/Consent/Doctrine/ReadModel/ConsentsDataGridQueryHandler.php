<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent\Doctrine\ReadModel;

use App\Domain\Consent\Consent;
use App\Domain\ConsentSettings\ConsentSettings;
use App\Infrastructure\DataGridQueryHandlerTrait;
use App\ReadModel\Consent\ConsentListView;
use App\ReadModel\Consent\ConsentsDataGridQuery;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
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
            function () use ($query): QueryBuilder {
                return $this->em->createQueryBuilder()
                    ->select('COUNT_ROWS()')
                    ->from(Consent::class, 'c')
                    ->andWhere('c.projectId = :projectId')
                    ->setParameter('projectId', $query->projectId());
            },
            function () use ($query): QueryBuilder {
                return $this->em->createQueryBuilder()
                    ->select('c.id, c.createdAt, c.lastUpdateAt, c.userIdentifier, c.settingsChecksum, cs.shortIdentifier AS settingsShortIdentifier, cs.id AS settingsId')
                    ->from(Consent::class, 'c')
                    ->leftJoin(ConsentSettings::class, 'cs', Join::WITH, 'cs.projectId = c.projectId AND cs.checksum = c.settingsChecksum')
                    ->andWhere('c.projectId = :projectId')
                    ->setParameter('projectId', $query->projectId());
            },
            ConsentListView::class,
            [
                'userIdentifier' => ['applyEquals', 'c.userIdentifier'],
                'createdAt' => ['applyDate', 'c.createdAt'],
                'lastUpdateAt' => ['applyDate', 'c.lastUpdateAt'],
            ],
            [
                'userIdentifier' => 'c.userIdentifier',
                'createdAt' => 'c.createdAt',
                'lastUpdateAt' => 'c.lastUpdateAt',
            ],
        );
    }
}
