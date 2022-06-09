<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent\Doctrine\ReadModel;

use App\Domain\Consent\Consent;
use Doctrine\ORM\AbstractQuery;
use App\ReadModel\Consent\ConsentView;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Consent\GetConsentByIdAndProjectIdQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class GetConsentByIdAndProjectIdQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\Consent\GetConsentByIdAndProjectIdQuery $query
	 *
	 * @return \App\ReadModel\Consent\ConsentView|NULL
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(GetConsentByIdAndProjectIdQuery $query): ?ConsentView
	{
		$data = $this->em->createQueryBuilder()
			->select('c')
			->from(Consent::class, 'c')
			->where('c.id = :id')
			->andWhere('c.projectId = :projectId')
			->setParameters([
				'id' => $query->id(),
				'projectId' => $query->projectId(),
			])
			->getQuery()
			->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

		return NULL !== $data ? $this->viewFactory->create(ConsentView::class, DoctrineViewData::create($data)) : NULL;
	}
}
