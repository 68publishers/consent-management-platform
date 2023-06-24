<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie\Doctrine\ReadModel;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Cookie\CookieDataForSuggestion;
use App\ReadModel\Cookie\FindCookieDataForSuggestionQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class FindCookieDataForSuggestionQueryHandler implements QueryHandlerInterface
{
	private EntityManagerInterface $em;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	/**
	 * @return array<CookieDataForSuggestion>
	 * @throws Exception
	 */
	public function __invoke(FindCookieDataForSuggestionQuery $query): array
	{
		$connection = $this->em->getConnection();

		$result = [];
		$rows = $connection->createQueryBuilder()
			->select('c.id, c.name, c.domain')
			->addSelect('cat.id AS category_id, cat.code AS category_code')
			->addSelect('cp.id AS provider_id, cp.code AS provider_code, cp.name AS provider_name')
			->addSelect('(p_has_cp.id IS NOT NULL OR p.cookie_provider_id = cp.id) AS associated')
			->from('cookie', 'c')
			->join('c', 'category', 'cat', 'c.category_id = cat.id AND cat.deleted_at IS NULL')
			->join('c', 'cookie_provider', 'cp', 'c.cookie_provider_id = cp.id AND cp.deleted_at IS NULL')
			->leftJoin('c', 'project', 'p', 'p.id = :projectId AND p.deleted_at IS NULL')
			->leftJoin('cp', 'project_has_cookie_provider', 'p_has_cp', 'p_has_cp.cookie_provider_id = cp.id AND p_has_cp.project_id = p.id')
			->where('c.deleted_at IS NULL')
			->andWhere('cp.private = false OR cp.id = p.cookie_provider_id')
			->setParameters([
				'projectId' => $query->projectId(),
			])
			->fetchAllAssociative();

		foreach ($rows as $row) {
			$result[] = new CookieDataForSuggestion(
				$row['id'],
				$row['name'],
				$row['domain'],
				$row['category_id'],
				$row['category_code'],
				$row['provider_id'],
				$row['provider_code'],
				$row['provider_name'],
				(bool) $row['associated'],
			);
		}

		return $result;
	}
}
