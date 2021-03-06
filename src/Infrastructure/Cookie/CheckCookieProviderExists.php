<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie;

use App\Domain\Cookie\CheckCookieProviderExistsInterface;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\ReadModel\CookieProvider\GetCookieProviderByIdQuery;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\Domain\CookieProvider\Exception\CookieProviderNotFoundException;

final class CheckCookieProviderExists implements CheckCookieProviderExistsInterface
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
	public function __invoke(CookieProviderId $cookieProviderId): void
	{
		if (NULL === $this->queryBus->dispatch(GetCookieProviderByIdQuery::create($cookieProviderId->toString()))) {
			throw CookieProviderNotFoundException::withId($cookieProviderId);
		}
	}
}
