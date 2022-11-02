<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent\Doctrine\ReadModel;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Consent\Consent;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Consent\CalculateLastConsentDateQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class CalculateLastConsentDateQueryHandler implements QueryHandlerInterface
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
	 * @param \App\ReadModel\Consent\CalculateLastConsentDateQuery $query
	 *
	 * @return \DateTimeImmutable|NULL
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 * @throws \Exception
	 */
	public function __invoke(CalculateLastConsentDateQuery $query): ?DateTimeImmutable
	{
		$result = $this->em->createQueryBuilder()
			->select('MAX(c.lastUpdateAt) AS last_consent_date')
			->from(Consent::class, 'c')
			->where('c.projectId = :projectId AND c.lastUpdateAt <= :maxDate')
			->setParameters([
				'projectId' => $query->projectId(),
				'maxDate' => $query->maxDate(),
			])
			->getQuery()
			->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

		return NULL !== $result && isset($result['last_consent_date']) ? new DateTimeImmutable($result['last_consent_date'], new DateTimeZone('UTC')) : NULL;
	}
}
