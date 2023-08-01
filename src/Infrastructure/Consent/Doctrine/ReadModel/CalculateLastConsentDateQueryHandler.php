<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent\Doctrine\ReadModel;

use App\Domain\Consent\Consent;
use App\ReadModel\Consent\CalculateLastConsentDateQuery;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class CalculateLastConsentDateQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * @throws NonUniqueResultException
     * @throws Exception
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

        return null !== $result && isset($result['last_consent_date']) ? new DateTimeImmutable($result['last_consent_date'], new DateTimeZone('UTC')) : null;
    }
}
