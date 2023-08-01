<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Project\ValueObject\Template;
use App\Domain\Shared\ValueObject\Locale;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectTemplateChanged extends AbstractDomainEvent
{
    private ProjectId $projectId;

    private Template $template;

    private Locale $locale;

    public static function create(ProjectId $projectId, Template $template, Locale $locale): self
    {
        $event = self::occur($projectId->toString(), [
            'template' => $template->value(),
            'locale' => $locale->value(),
        ]);

        $event->projectId = $projectId;
        $event->template = $template;
        $event->locale = $locale;

        return $event;
    }

    public function projectId(): ProjectId
    {
        return $this->projectId;
    }

    public function template(): Template
    {
        return $this->template;
    }

    public function locale(): Locale
    {
        return $this->locale;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
        $this->template = Template::fromValue($parameters['template']);
        $this->locale = Locale::fromValue($parameters['locale']);
    }
}
