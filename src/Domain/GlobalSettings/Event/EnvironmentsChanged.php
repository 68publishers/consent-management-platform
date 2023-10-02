<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Event;

use App\Domain\GlobalSettings\ValueObject\Environments;
use App\Domain\GlobalSettings\ValueObject\GlobalSettingsId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class EnvironmentsChanged extends AbstractDomainEvent
{
    private GlobalSettingsId $globalSettingsId;

    private Environments $environments;

    public static function create(GlobalSettingsId $globalSettingsId, Environments $environments): self
    {
        $event = self::occur($globalSettingsId->toString(), [
            'environments' => $environments->toArray(),
        ]);

        $event->globalSettingsId = $globalSettingsId;
        $event->environments = $environments;

        return $event;
    }

    public function globalSettingsId(): GlobalSettingsId
    {
        return $this->globalSettingsId;
    }

    public function environments(): Environments
    {
        return $this->environments;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->globalSettingsId = GlobalSettingsId::fromUuid($this->aggregateId()->id());
        $this->environments = Environments::reconstitute($parameters['environments']);
    }
}
