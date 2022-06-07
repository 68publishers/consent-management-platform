<?php

declare(strict_types=1);

namespace App\Infrastructure\CookieProvider\Doctrine\QueryHandler;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\CookieProvider\CookieProvider;
use App\Infrastructure\DataGridQueryHandlerTrait;
use App\ReadModel\CookieProvider\CookieProviderView;
use App\ReadModel\CookieProvider\CookieProvidersDataGridQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class CookieProviderDataGridQueryHandler implements QueryHandlerInterface
{
	use DataGridQueryHandlerTrait;

	private EntityManagerInterface $em;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface $em
	 */
	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	/**
	 * @param \App\ReadModel\CookieProvider\CookieProvidersDataGridQuery $query
	 *
	 * @return array|int
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(CookieProvidersDataGridQuery $query)
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
				return $this->em->createQueryBuilder()
					->select('c')
					->from(CookieProvider::class, 'c')
					->where('c.deletedAt IS NULL');
			},
			static fn (array $data): CookieProviderView => ViewFactory::createCookieProviderView($data),
			[
				'name' => ['applyLike', 'c.name'],
				'code' => ['applyLike', 'c.code'],
				'link' => ['applyLike', 'c.link'],
				'type' => ['applyEquals', 'c.type'],
				'createdAt' => ['applyDate', 'c.createdAt'],
			],
			[
				'name' => 'c.name',
				'code' => 'c.code',
				'createdAt' => 'c.createdAt',
			]
		);
	}
}
