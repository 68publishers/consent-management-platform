<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\ReadModel;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Project\ProjectTranslation;
use App\ReadModel\Project\ProjectTemplateView;
use App\ReadModel\Project\FindProjectTemplatesQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class FindProjectTemplatesQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\Project\FindProjectTemplatesQuery $query
	 *
	 * @return \App\ReadModel\Project\ProjectTemplateView[]
	 */
	public function __invoke(FindProjectTemplatesQuery $query): array
	{
		$data = $this->em->createQueryBuilder()
			->select('p.id AS projectId, pt.template AS template, pt.locale AS templateLocale, p.locales.locales, p.locales.defaultLocale')
			->from(ProjectTranslation::class, 'pt')
			->join('pt.project', 'p', Join::WITH, 'p.id = :projectId AND p.deletedAt IS NULL')
			->setParameter('projectId', $query->projectId())
			->getQuery()
			->getResult(AbstractQuery::HYDRATE_ARRAY);

		return array_map(fn (array $row): ProjectTemplateView => $this->viewFactory->create(ProjectTemplateView::class, DoctrineViewData::create($row)), $data);
	}
}
