<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\QueryHandler;

use App\Domain\Project\Project;
use Doctrine\ORM\AbstractQuery;
use App\ReadModel\Project\ProjectView;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Project\FindAllProjectsQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class FindAllProjectsQueryHandler implements QueryHandlerInterface
{
	private EntityManagerInterface $em;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface $em
	 */
	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	/**
	 * @param \App\ReadModel\Project\FindAllProjectsQuery $query
	 *
	 * @return \App\ReadModel\Project\ProjectView[]
	 */
	public function __invoke(FindAllProjectsQuery $query): array
	{
		$data = $this->em->createQueryBuilder()
			->select('p')
			->from(Project::class, 'p')
			->orderBy('p.createdAt', 'DESC')
			->getQuery()
			->getResult(AbstractQuery::HYDRATE_ARRAY);

		return array_map(static fn (array $row): ProjectView => ProjectView::fromArray($row), $data);
	}
}
