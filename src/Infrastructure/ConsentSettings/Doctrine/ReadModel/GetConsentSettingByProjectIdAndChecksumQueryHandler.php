<?php

declare(strict_types=1);

namespace App\Infrastructure\ConsentSettings\Doctrine\ReadModel;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\ConsentSettings\ConsentSettings;
use App\ReadModel\ConsentSettings\ConsentSettingsView;
use App\ReadModel\ConsentSettings\GetConsentSettingsByProjectIdAndChecksumQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class GetConsentSettingByProjectIdAndChecksumQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\ConsentSettings\GetConsentSettingsByProjectIdAndChecksumQuery $query
	 *
	 * @return \App\ReadModel\ConsentSettings\ConsentSettingsView|NULL
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(GetConsentSettingsByProjectIdAndChecksumQuery $query): ?ConsentSettingsView
	{
		$data = $this->em->createQueryBuilder()
			->select('cs')
			->from(ConsentSettings::class, 'cs')
			->where('cs.projectId = :projectId')
			->andWhere('cs.checksum = :checksum')
			->setParameters([
				'projectId' => $query->projectId(),
				'checksum' => $query->checksum(),
			])
			->getQuery()
			->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

		return NULL !== $data ? $this->viewFactory->create(ConsentSettings::class, DoctrineViewData::create($data)) : NULL;
	}
}
