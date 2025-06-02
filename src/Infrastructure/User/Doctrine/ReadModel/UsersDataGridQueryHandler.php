<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Doctrine\ReadModel;

use App\Infrastructure\DataGridQueryHandlerTrait;
use App\ReadModel\User\UsersDataGridQuery;
use App\ReadModel\User\UserView;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\UserBundle\Domain\Aggregate\User;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query')]
final class UsersDataGridQueryHandler implements QueryHandlerInterface
{
    use DataGridQueryHandlerTrait;

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function __invoke(UsersDataGridQuery $query): array|int
    {
        return $this->processQuery(
            $query,
            function (): QueryBuilder {
                return $this->em->createQueryBuilder()
                    ->select('COUNT(u.id)')
                    ->from(User::class, 'u')
                    ->where('u.deletedAt IS NULL');
            },
            function (): QueryBuilder {
                return $this->em->createQueryBuilder()
                    ->select('u')
                    ->from(User::class, 'u')
                    ->where('u.deletedAt IS NULL');
            },
            UserView::class,
            [
                'id' => ['applyLike', 'CAST(u.id AS TEXT)'],
                'emailAddress' => ['applyLike', 'u.emailAddress'],
                'name' => ['applyLike', 'CONCAT(u.name.firstname, \' \', u.name.surname)'],
                'createdAt' => ['applyDate', 'u.createdAt'],
                'roles' => ['applyJsonbContains', 'u.roles'],
            ],
            [
                'emailAddress' => 'u.emailAddress',
                'name' => ['u.name.firstname', 'u.name.surname'],
                'createdAt' => 'u.createdAt',
            ],
        );
    }
}
