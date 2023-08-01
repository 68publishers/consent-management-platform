<?php

declare(strict_types=1);

namespace App\Infrastructure\ConsentSettings\Doctrine\ReadModel;

use App\Domain\ConsentSettings\ConsentSettings;
use App\Domain\Project\Project;
use App\ReadModel\ConsentSettings\GetLatestShortIdentifierQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class GetLatestShortIdentifierQueryHandler implements QueryHandlerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(GetLatestShortIdentifierQuery $query): int
    {
        try {
            return (int) $this->em->createQueryBuilder()
                ->select('MAX(cs.shortIdentifier)')
                ->from(ConsentSettings::class, 'cs')
                ->join(Project::class, 'p', Join::WITH, 'cs.projectId = p.id AND p.id = :projectId AND p.deletedAt IS NULL')
                ->setParameters([
                    'projectId' => $query->projectId(),
                ])
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }
}
