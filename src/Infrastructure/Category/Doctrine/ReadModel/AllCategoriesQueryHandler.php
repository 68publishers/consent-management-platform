<?php

declare(strict_types=1);

namespace App\Infrastructure\Category\Doctrine\ReadModel;

use App\Domain\Category\Category;
use App\ReadModel\Category\AllCategoriesQuery;
use App\ReadModel\Category\CategoryView;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;

final class AllCategoriesQueryHandler implements QueryHandlerInterface
{
    private EntityManagerInterface $em;

    private ViewFactoryInterface $viewFactory;

    public function __construct(EntityManagerInterface $em, ViewFactoryInterface $viewFactory)
    {
        $this->em = $em;
        $this->viewFactory = $viewFactory;
    }

    /**
     * @return CategoryView[]
     */
    public function __invoke(AllCategoriesQuery $query): array
    {
        $data = $this->em->createQueryBuilder()
            ->select('c, ct')
            ->from(Category::class, 'c')
            ->leftJoin('c.translations', 'ct')
            ->where('c.deletedAt IS NULL')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);

        return array_map(fn (array $row): CategoryView => $this->viewFactory->create(CategoryView::class, DoctrineViewData::create($row)), $data);
    }
}
