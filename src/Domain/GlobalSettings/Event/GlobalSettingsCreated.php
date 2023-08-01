<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Event;

use App\Domain\GlobalSettings\ValueObject\GlobalSettingsId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class GlobalSettingsCreated extends AbstractDomainEvent
{
    private GlobalSettingsId $globalSettingsId;

    /**
     * @return static
     */
    public static function create(GlobalSettingsId $globalSettingsId): self
    {
        $event = self::occur($globalSettingsId->toString());

        $event->globalSettingsId = $globalSettingsId;

        return $event;
    }

    public function globalSettingsId(): GlobalSettingsId
    {
        return $this->globalSettingsId;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->globalSettingsId = GlobalSettingsId::fromUuid($this->aggregateId()->id());
    }
}
