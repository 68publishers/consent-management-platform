<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\ReadModel;

use App\Domain\Project\Project;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Project\ProjectTemplateView;
use App\ReadModel\Project\GetProjectTemplateByCodeAndLocaleWithFallbackQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ViewFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Doctrine\ReadModel\DoctrineViewData;

final class GetProjectTemplateByCodeAndLocaleWithFallbackQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\Project\GetProjectTemplateByCodeAndLocaleWithFallbackQuery $query
	 *
	 * @return \App\ReadModel\Project\ProjectTemplateView|NULL
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function __invoke(GetProjectTemplateByCodeAndLocaleWithFallbackQuery $query): ?ProjectTemplateView
	{
		$qb = $this->em->createQueryBuilder()
			->select('p.id AS projectId, p.locales.locales, p.locales.defaultLocale')
			->from(Project::class, 'p')
			->where('LOWER(p.code) = LOWER(:code) AND p.deletedAt IS NULL')
			->setParameter('code', $query->code());

		if (NULL !== $query->locale()) {
			$qb->addSelect('COALESCE(NULLIF(pt.template, \'\'), pt_default.template, \'\') AS template')
				->addSelect('COALESCE(pt.locale, p.locales.defaultLocale) AS templateLocale')
				->leftJoin('p.translations', 'pt_default', Join::WITH, 'pt_default.locale = p.locales.defaultLocale')
				->leftJoin('p.translations', 'pt', Join::WITH, 'pt.locale = :locale')
				->setParameter('locale', $query->locale());
		} else {
			$qb->addSelect('COALESCE(pt_default.template, \'\') AS template')
				->addSelect('COALESCE(pt_default.locale, p.locales.defaultLocale) AS templateLocale')
				->leftJoin('p.translations', 'pt_default', Join::WITH, 'pt_default.locale = p.locales.defaultLocale');
		}

		$data = $qb->getQuery()
			->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

		return $this->viewFactory->create(ProjectTemplateView::class, DoctrineViewData::create($data));
	}
}
