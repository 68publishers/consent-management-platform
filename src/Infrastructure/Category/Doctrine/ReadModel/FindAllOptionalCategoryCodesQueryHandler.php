<?php

declare(strict_types=1);

namespace App\Infrastructure\Category\Doctrine\ReadModel;

use App\Domain\Category\Category;
use App\ReadModel\Category\FindAllOptionalCategoryCodesQuery;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class FindAllOptionalCategoryCodesQueryHandler implements QueryHandlerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return array<string>
     */
    public function __invoke(FindAllOptionalCategoryCodesQuery $query): array
    {
        return $this->em->createQueryBuilder()
            ->select('c.code')
            ->from(Category::class, 'c')
            ->where('c.deletedAt IS NULL AND c.necessary = false')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_SCALAR_COLUMN);
    }
}
