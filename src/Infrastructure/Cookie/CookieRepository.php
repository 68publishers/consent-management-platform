<?php

declare(strict_types=1);

namespace App\Infrastructure\Cookie;

use App\Domain\Cookie\Cookie;
use App\Domain\Cookie\CookieRepositoryInterface;
use App\Domain\Cookie\Exception\CookieNotFoundException;
use App\Domain\Cookie\ValueObject\CookieId;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;
use SixtyEightPublishers\ArchitectureBundle\Infrastructure\Common\Repository\AggregateRootRepositoryInterface;

final class CookieRepository implements CookieRepositoryInterface
{
    private AggregateRootRepositoryInterface $aggregateRootRepository;

    public function __construct(AggregateRootRepositoryInterface $aggregateRootRepository)
    {
        $this->aggregateRootRepository = $aggregateRootRepository;
    }

    public function save(Cookie $cookie): void
    {
        $this->aggregateRootRepository->saveAggregateRoot($cookie);
    }

    public function get(CookieId $id): Cookie
    {
        $cookie = $this->aggregateRootRepository->loadAggregateRoot(Cookie::class, AggregateId::fromUuid($id->id()));

        if (!$cookie instanceof Cookie) {
            throw CookieNotFoundException::withId($id);
        }

        return $cookie;
    }
}
