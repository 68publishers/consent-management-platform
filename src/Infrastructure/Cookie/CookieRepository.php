<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie;

use App\Domain\Cookie\Cookie;
use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Cookie\CookieRepositoryInterface;
use App\Domain\Cookie\Exception\CookieNotFoundException;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Common\Repository\AggregateRootRepositoryInterface;

final class CookieRepository implements CookieRepositoryInterface
{
	private AggregateRootRepositoryInterface $aggregateRootRepository;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Infrastructure\Common\Repository\AggregateRootRepositoryInterface $aggregateRootRepository
	 */
	public function __construct(AggregateRootRepositoryInterface $aggregateRootRepository)
	{
		$this->aggregateRootRepository = $aggregateRootRepository;
	}

	/**
	 * {@inheritDoc}
	 */
	public function save(Cookie $cookie): void
	{
		$this->aggregateRootRepository->saveAggregateRoot($cookie);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get(CookieId $id): Cookie
	{
		$cookie = $this->aggregateRootRepository->loadAggregateRoot(Cookie::class, AggregateId::fromUuid($id->id()));

		if (!$cookie instanceof Cookie) {
			throw CookieNotFoundException::withId($id);
		}

		return $cookie;
	}
}
