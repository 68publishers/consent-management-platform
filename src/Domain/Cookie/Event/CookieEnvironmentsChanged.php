<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Event;

use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Cookie\ValueObject\Environments;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieEnvironmentsChanged extends AbstractDomainEvent
{
    private CookieId $cookieId;

    private bool $allEnvironments;

    private Environments $environments;

    public static function create(
        CookieId $cookieId,
        bool $allEnvironments,
        Environments $environments,
    ): self {
        $event = self::occur($cookieId->toString(), [
            'all_environments' => $allEnvironments,
            'environments' => $environments->toArray(),
        ]);

        $event->allEnvironments = $allEnvironments;
        $event->environments = $environments;

        return $event;
    }

    public function cookieId(): CookieId
    {
        return $this->cookieId;
    }

    public function allEnvironments(): bool
    {
        return $this->allEnvironments;
    }

    public function environments(): Environments
    {
        return $this->environments;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->cookieId = CookieId::fromUuid($this->aggregateId()->id());
        $this->allEnvironments = (bool) $parameters['all_environments'];
        $this->environments = Environments::reconstitute($parameters['environments']);
    }
}
