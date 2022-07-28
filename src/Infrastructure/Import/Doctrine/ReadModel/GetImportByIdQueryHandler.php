<?php

declare(strict_types=1);

namespace App\Infrastructure\Import\Doctrine\ReadModel;

use App\Domain\Import\Import;
use Doctrine\ORM\AbstractQuery;
use App\ReadModel\Import\ImportView;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Import\GetImportByIdQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class GetImportByIdQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\Import\GetImportByIdQuery $query
	 *
	 * @return \App\ReadModel\Import\ImportView|NULL
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(GetImportByIdQuery $query): ?ImportView
	{
		$data = $this->em->createQueryBuilder()
			->select('i')
			->from(Import::class, 'i')
			->where('i.id = :id')
			->setParameter('id', $query->id())
			->getQuery()
			->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

		return NULL !== $data ? $this->viewFactory->create(ImportView::class, DoctrineViewData::create($data)) : NULL;
	}
}
