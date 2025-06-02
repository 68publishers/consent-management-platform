<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\ReadModel;

use App\Infrastructure\DataGridQueryHandlerTrait;
use App\ReadModel\Project\ProjectCookieSuggestionsDataGridQuery;
use App\ReadModel\Project\ProjectCookieSuggestionsListingItem;
use App\ReadModel\Project\ProjectCookieSuggestionsStatistics;
use DateTimeImmutable;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query')]
final class ProjectCookieSuggestionsDataGridQueryHandler implements QueryHandlerInterface
{
    use DataGridQueryHandlerTrait;

    /**
     * @throws NonUniqueResultException
     * @throws Exception
     * @throws NoResultException
     * @throws \Exception
     */
    public function __invoke(ProjectCookieSuggestionsDataGridQuery $query): array|int
    {
        return $this->processQuery(
            $query,
            function (): QueryBuilder {
                return $this->em->getConnection()->createQueryBuilder()
                    ->select('COUNT(*)')
                    ->from('project', 'p')
                    ->join('p', 'project_cookie_suggestion_statistics', 'ps', 'ps.project_id = p.id')
                    ->andWhere('ps.total_without_virtual > 0');
            },
            function (): QueryBuilder {
                return $this->em->getConnection()->createQueryBuilder()
                    ->select('p.id, p.code, p.name')
                    ->addSelect('ps.missing, ps.unassociated, ps.problematic, ps.unproblematic, ps.ignored, ps.total, ps.total_without_virtual, ps.latest_found_at')
                    ->from('project', 'p')
                    ->join('p', 'project_cookie_suggestion_statistics', 'ps', 'ps.project_id = p.id')
                    ->andWhere('ps.total_without_virtual > 0');
            },
            static fn (array $row): ProjectCookieSuggestionsListingItem => new ProjectCookieSuggestionsListingItem(
                $row['id'],
                $row['code'],
                $row['name'],
                new ProjectCookieSuggestionsStatistics(
                    $row['missing'],
                    $row['unassociated'],
                    $row['problematic'],
                    $row['unproblematic'],
                    $row['ignored'],
                    $row['total'],
                    $row['total_without_virtual'],
                    null !== $row['latest_found_at'] ? new DateTimeImmutable($row['latest_found_at']) : null,
                ),
            ),
            [
                'code' => ['applyLike', 'p.code'],
                'name' => ['applyLike', 'p.name'],
            ],
            [
                'code' => 'p.code',
                'name' => 'p.name',
                'statisticsTotal' => 'ps.total',
                'latestFoundAt' => 'ps.latest_found_at',
            ],
        );
    }
}
