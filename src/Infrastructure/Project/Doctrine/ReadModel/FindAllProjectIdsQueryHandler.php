<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\ReadModel;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Project\FindAllProjectIdsQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class FindAllProjectIdsQueryHandler implements QueryHandlerInterface
{
	private EntityManagerInterface $em;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	/**
	 * @return array<string>
	 * @throws Exception
	 */
	public function __invoke(FindAllProjectIdsQuery $query): array
	{
		$row = $this->em->getConnection()->createQueryBuilder()
			->select('p.id')
			->from('project', 'p')
			->where('p.deleted_at IS NULL')
			->fetchAllAssociative();

		return array_values(
			array_map(
				static fn (array $row): string => $row['id'],
				$row,
			),
		);
	}
}