<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie\Doctrine\ReadModel;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use App\ReadModel\Cookie\GetCookieProviderIdByCookieIdQuery;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\QueryHandlerInterface;

final class GetCookieProviderIdByCookieIdQueryHandler implements QueryHandlerInterface
{
	private EntityManagerInterface $em;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	/**
	 * @throws Exception
	 */
	public function __invoke(GetCookieProviderIdByCookieIdQuery $query): ?string
	{
		$cookieProviderId = $this->em->getConnection()->createQueryBuilder()
			->select('c.cookie_provider_id')
			->from('cookie', 'c')
			->join('c', 'cookie_provider', 'cp', 'cp.id = c.cookie_provider_id AND cp.deleted_at IS NULL')
			->where('c.id = :cookieId')
			->andWhere('c.deleted_at IS NULL')
			->setParameter('cookieId', $query->cookieId(), Types::GUID)
			->setMaxResults(1)
			->fetchOne();

		return $cookieProviderId ?: NULL;
	}
}
