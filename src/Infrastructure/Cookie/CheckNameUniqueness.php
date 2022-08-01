<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie;

use App\ReadModel\Cookie\CookieView;
use App\Domain\Cookie\ValueObject\Name;
use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Cookie\CheckNameUniquenessInterface;
use App\Domain\Cookie\Exception\NameUniquenessException;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\ReadModel\Cookie\GetCookieByNameAndCookieProviderQuery;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;

final class CheckNameUniqueness implements CheckNameUniquenessInterface
{
	private QueryBusInterface $queryBus;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface $queryBus
	 */
	public function __construct(QueryBusInterface $queryBus)
	{
		$this->queryBus = $queryBus;
	}

	/**
	 * {@inheritDoc}
	 */
	public function __invoke(CookieId $cookieId, Name $name, CookieProviderId $cookieProviderId): void
	{
		$cookieView = $this->queryBus->dispatch(GetCookieByNameAndCookieProviderQuery::create($name->value(), $cookieProviderId->toString()));

		if (!$cookieView instanceof CookieView) {
			return;
		}

		if (!$cookieView->id->equals($cookieId)) {
			throw NameUniquenessException::create($name->value(), $cookieProviderId->toString());
		}
	}
}
