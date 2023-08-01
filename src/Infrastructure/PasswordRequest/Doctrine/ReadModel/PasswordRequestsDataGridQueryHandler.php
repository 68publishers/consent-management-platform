<?php

declare(strict_types=1);

namespace App\Infrastructure\PasswordRequest\Doctrine\ReadModel;

use App\Infrastructure\DataGridQueryHandlerTrait;
use App\ReadModel\PasswordRequest\PasswordRequestsDataGridQuery;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\Aggregate\PasswordRequest;
use SixtyEightPublishers\ForgotPasswordBundle\ReadModel\View\PasswordRequestView;

final class PasswordRequestsDataGridQueryHandler implements QueryHandlerInterface
{
    use DataGridQueryHandlerTrait;

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function __invoke(PasswordRequestsDataGridQuery $query): array|int
    {
        return $this->processQuery(
            $query,
            function (): QueryBuilder {
                return $this->em->createQueryBuilder()
                    ->select('COUNT(pr.id)')
                    ->from(PasswordRequest::class, 'pr');
            },
            function (): QueryBuilder {
                return $this->em->createQueryBuilder()
                    ->select('pr')
                    ->from(PasswordRequest::class, 'pr');
            },
            PasswordRequestView::class,
            [
                'id' => ['applyLike', 'CAST(pr.id AS TEXT)'],
                'emailAddress' => ['applyLike', 'pr.emailAddress'],
                'status' => ['applyIn', 'pr.status'],
                'requestedAt' => ['applyDate', 'pr.requestedAt'],
                'finishedAt' => ['applyDate', 'pr.finishedAt'],
            ],
            [
                'emailAddress' => 'pr.emailAddress',
                'requestedAt' => 'pr.requestedAt',
                'finishedAt' => 'pr.finishedAt',
            ],
        );
    }
}
