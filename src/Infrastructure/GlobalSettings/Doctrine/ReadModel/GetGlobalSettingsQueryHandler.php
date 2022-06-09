<?php

declare(strict_types=1);

namespace App\Infrastructure\GlobalSettings\Doctrine\ReadModel;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\GlobalSettings\GlobalSettings;
use App\ReadModel\GlobalSettings\GlobalSettingsView;
use App\ReadModel\GlobalSettings\GetGlobalSettingsQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class GetGlobalSettingsQueryHandler implements QueryHandlerInterface
{
	private EntityManagerInterface $em;

	private ViewFactoryInterface $viewFactory;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface                                         $em
	 * @param \SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface $viewFactory
	 */
	public function __construct(EntityManagerInterface $em, ViewFactoryInterface $viewFactory)
	{
		$this->em = $em;
		$this->viewFactory = $viewFactory;
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

		return NULL !== $data ? $this->viewFactory->create(GlobalSettingsView::class, DoctrineViewData::create($data)) : NULL;
	}
}
