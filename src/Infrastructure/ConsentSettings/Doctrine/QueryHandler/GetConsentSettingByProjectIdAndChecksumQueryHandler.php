<?php

declare(strict_types=1);

namespace App\Infrastructure\ConsentSettings\Doctrine\QueryHandler;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\ConsentSettings\ConsentSettings;
use App\ReadModel\ConsentSettings\ConsentSettingsView;
use App\ReadModel\ConsentSettings\GetConsentSettingsByProjectIdAndChecksumQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class GetConsentSettingByProjectIdAndChecksumQueryHandler implements QueryHandlerInterface
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

		return NULL !== $data ? ConsentSettingsView::fromArray($data) : NULL;
	}
}
