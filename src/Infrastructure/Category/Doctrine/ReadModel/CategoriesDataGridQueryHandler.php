<?php

declare(strict_types=1);

namespace App\Infrastructure\Category\Doctrine\ReadModel;

use App\Domain\Category\Category;
use App\Infrastructure\DataGridQueryHandlerTrait;
use App\ReadModel\Category\CategoriesDataGridQuery;
use App\ReadModel\Category\CategoryView;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query')]
final class CategoriesDataGridQueryHandler implements QueryHandlerInterface
{
    use DataGridQueryHandlerTrait;

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function __invoke(CategoriesDataGridQuery $query): array|int
    {
        return $this->processQuery(
            $query,
            function (CategoriesDataGridQuery $query): QueryBuilder {
                return $this->em->createQueryBuilder()
                    ->select('COUNT(c.id)')
                    ->from(Category::class, 'c')
                    ->leftJoin('c.translations', 'ct', Join::WITH, 'ct.locale = :locale')
                    ->where('c.deletedAt IS NULL')
                    ->setParameter('locale', $query->locale() ?? '_unknown_');
            },
            function (CategoriesDataGridQuery $query): QueryBuilder {
                return $this->em->createQueryBuilder()
                    ->select('c, ct')
                    ->from(Category::class, 'c')
                    ->leftJoin('c.translations', 'ct', Join::WITH, 'ct.locale = :locale')
                    ->where('c.deletedAt IS NULL')
                    ->setParameter('locale', $query->locale() ?? '_unknown_');
            },
            CategoryView::class,
            [
                'name' => ['applyLike', 'ct.name'],
                'code' => ['applyLike', 'c.code'],
                'createdAt' => ['applyDate', 'c.createdAt'],
                'active' => ['applyEquals', 'c.active'],
            ],
            [
                'name' => 'ct.name',
                'code' => 'c.code',
                'createdAt' => 'c.createdAt',
            ],
        );
    }
}
