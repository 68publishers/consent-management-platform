<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion\Event;

use App\Domain\CookieSuggestion\ValueObject\CookieSuggestionId;
use App\Domain\CookieSuggestion\ValueObject\Domain;
use App\Domain\CookieSuggestion\ValueObject\Name;
use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieSuggestionCreated extends AbstractDomainEvent
{
    private CookieSuggestionId $cookieSuggestionId;

    private ProjectId $projectId;

    private Name $name;

    private Domain $domain;

    public static function create(
        CookieSuggestionId $cookieSuggestionId,
        ProjectId $projectId,
        Name $name,
        Domain $domain,
    ): self {
        $event = self::occur($cookieSuggestionId->toString(), [
            'project_id' => $projectId->toString(),
            'name' => $name->value(),
            'domain' => $domain->value(),
        ]);

        $event->cookieSuggestionId = $cookieSuggestionId;
        $event->projectId = $projectId;
        $event->name = $name;
        $event->domain = $domain;

        return $event;
    }

    public function cookieSuggestionId(): CookieSuggestionId
    {
        return $this->cookieSuggestionId;
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function domain(): Domain
    {
        return $this->domain;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->cookieSuggestionId = CookieSuggestionId::fromUuid($this->aggregateId()->id());
        $this->projectId = ProjectId::fromString($parameters['project_id']);
        $this->name = Name::fromValue($parameters['name']);
        $this->domain = Domain::fromValue($parameters['domain']);
    }
}
