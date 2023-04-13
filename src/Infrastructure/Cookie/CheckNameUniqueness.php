<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie;

use App\ReadModel\Cookie\CookieView;
use App\Domain\Cookie\ValueObject\Name;
use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Cookie\CheckNameUniquenessInterface;
use App\Domain\Cookie\Exception\NameUniquenessException;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\ReadModel\Cookie\GetCookieByNameAndCookieProviderAndCategoryQuery;

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
	public function __invoke(CookieId $cookieId, Name $name, CookieProviderId $cookieProviderId, CategoryId $categoryId): void
	{
		$cookieView = $this->queryBus->dispatch(GetCookieByNameAndCookieProviderAndCategoryQuery::create($name->value(), $cookieProviderId->toString(), $categoryId->toString()));

		if (!$cookieView instanceof CookieView) {
			return;
		}

		if (!$cookieView->id->equals($cookieId)) {
			throw NameUniquenessException::create($name->value(), $cookieProviderId->toString());
		}
	}
}
