<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieProvider\Doctrine\ReadModel;

use App\Domain\Cookie\Cookie;
use App\Domain\CookieProvider\CookieProvider;
use App\Infrastructure\DataGridQueryHandlerTrait;
use App\ReadModel\CookieProvider\CookieProviderDaraGridItemView;
use App\ReadModel\CookieProvider\CookieProvidersDataGridQuery;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class CookieProviderDataGridQueryHandler implements QueryHandlerInterface
{
    use DataGridQueryHandlerTrait;

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function __invoke(CookieProvidersDataGridQuery $query): array|int
    {
        return $this->processQuery(
            $query,
            function (): QueryBuilder {
                return $this->em->createQueryBuilder()
                    ->select('COUNT(c.id)')
                    ->from(CookieProvider::class, 'c')
                    ->where('c.deletedAt IS NULL');
            },
            function (): QueryBuilder {
                $numberOfCookiesSubQuery = $this->em->createQueryBuilder()
                    ->select('COUNT(cookie.id)')
                    ->from(Cookie::class, 'cookie')
                    ->where('cookie.cookieProviderId = c.id')
                    ->andWhere('cookie.deletedAt IS NULL')
                    ->getQuery()
                    ->getDQL();

                return $this->em->createQueryBuilder()
                    ->select('c.id, c.createdAt, c.code, c.type, c.name, c.link, c.private, c.active')
                    ->addSelect(sprintf(
                        '(%s) AS numberOfCookies',
                        $numberOfCookiesSubQuery,
                    ))
                    ->from(CookieProvider::class, 'c')
                    ->where('c.deletedAt IS NULL');
            },
            CookieProviderDaraGridItemView::class,
            [
                'name' => ['applyLike', 'c.name'],
                'code' => ['applyLike', 'c.code'],
                'link' => ['applyLike', 'c.link'],
                'type' => ['applyEquals', 'c.type'],
                'private' => ['applyEquals', 'c.private'],
                'createdAt' => ['applyDate', 'c.createdAt'],
                'active' => ['applyEquals', 'c.active'],
            ],
            [
                'name' => 'c.name',
                'code' => 'c.code',
                'createdAt' => 'c.createdAt',
                'numberOfCookies' => 'numberOfCookies',
            ],
        );
    }
}
