<?php

declare(strict_types=1);

namespace App\Infrastructure\ConsentSettings\Doctrine\ReadModel;

use App\Domain\Project\Project;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\ConsentSettings\ConsentSettings;
use App\ReadModel\ConsentSettings\ConsentSettingsView;
use App\ReadModel\ConsentSettings\GetConsentSettingsByIdAndProjectIdQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class GetConsentSettingByIdAndProjectIdQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\ConsentSettings\GetConsentSettingsByIdAndProjectIdQuery $query
	 *
	 * @return \App\ReadModel\ConsentSettings\ConsentSettingsView|null
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(GetConsentSettingsByIdAndProjectIdQuery $query): ?ConsentSettingsView
	{
		$data = $this->em->createQueryBuilder()
			->select('cs')
			->from(ConsentSettings::class, 'cs')
			->join(Project::class, 'p', Join::WITH, 'cs.projectId = p.id AND p.id = :projectId AND p.deletedAt IS NULL')
			->where('cs.id = :id')
			->setParameters([
				'projectId' => $query->projectId(),
				'id' => $query->id(),
			])
			->getQuery()
			->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

		return NULL !== $data ? $this->viewFactory->create(ConsentSettingsView::class, DoctrineViewData::create($data)) : NULL;
	}
}
