<?php

declare(strict_types=1);

namespace App\Infrastructure\GlobalSettings\Doctrine\QueryHandler;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\GlobalSettings\GlobalSettings;
use App\ReadModel\GlobalSettings\GlobalSettingsView;
use App\ReadModel\GlobalSettings\GetGlobalSettingsQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class GetGlobalSettingsQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\GlobalSettings\GetGlobalSettingsQuery $query
	 *
	 * @return \App\ReadModel\GlobalSettings\GlobalSettingsView|NULL
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(GetGlobalSettingsQuery $query): ?GlobalSettingsView
	{
		$data = $this->em->createQueryBuilder()
			->select('gs')
			->from(GlobalSettings::class, 'gs')
			->setMaxResults(1)
			->getQuery()
			->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

		return NULL !== $data ? GlobalSettingsView::fromArray($data) : NULL;
	}
}
