<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectCookieProviderAdded extends AbstractDomainEvent
{
    private ProjectId $projectId;

    private CookieProviderId $cookieProviderId;

    /**
     * @return static
     */
    public static function create(ProjectId $projectId, CookieProviderId $cookieProviderId): self
    {
        $event = self::occur($projectId->toString(), [
            'cookie_provider_id' => $cookieProviderId->toString(),
        ]);

        $event->projectId = $projectId;
        $event->cookieProviderId = $cookieProviderId;

        return $event;
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function cookieProviderId(): CookieProviderId
    {
        return $this->cookieProviderId;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
        $this->cookieProviderId = CookieProviderId::fromString($parameters['cookie_provider_id']);
    }
}
