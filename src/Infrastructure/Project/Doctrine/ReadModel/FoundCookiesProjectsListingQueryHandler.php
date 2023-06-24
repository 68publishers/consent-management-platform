<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\ReadModel;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Project\FoundCookieProjectListingItem;
use App\ReadModel\Project\FoundCookiesProjectsListingQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class FoundCookiesProjectsListingQueryHandler implements QueryHandlerInterface
{
	private EntityManagerInterface $em;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	/**
	 * @return array<FoundCookieProjectListingItem>
	 * @throws Exception
	 */
	public function __invoke(FoundCookiesProjectsListingQuery $query): array
	{
		$connection = $this->em->getConnection();

		$result = [];
		$rows = $connection->createQueryBuilder()
			->select('p.id, p.name, p.code, p.color, COUNT(cs.id) AS found_cookies')
			->from('project', 'p')
			->leftJoin('p', 'cookie_suggestion', 'cs', 'p.id = cs.project_id')
			->where('p.deleted_at IS NULL')
			->orderBy('p.name', 'ASC')
			->groupBy('p.id')
			->fetchAllAssociative();

		foreach ($rows as $row) {
			$result[] = new FoundCookieProjectListingItem(
				$row['id'],
				$row['name'],
				$row['code'],
				$row['color'],
				$row['found_cookies'],
			);
		}

		return $result;
	}
}
