<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\Project\ValueObject\Code;
use App\Domain\Project\ValueObject\Color;
use App\Domain\Project\ValueObject\Description;
use App\Domain\Project\ValueObject\Domain;
use App\Domain\Project\ValueObject\Environments;
use App\Domain\Project\ValueObject\Name;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Shared\ValueObject\Locales;
use App\Domain\Shared\ValueObject\LocalesConfig;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectCreated extends AbstractDomainEvent
{
    private ProjectId $projectId;

    private CookieProviderId $cookieProviderId;

    private Name $name;

    private Code $code;

    private Domain $domain;

    private Description $description;

    private Color $color;

    private bool $active;

    private LocalesConfig $locales;

    private Environments $environments;

    public static function create(
        ProjectId $projectId,
        CookieProviderId $cookieProviderId,
        Name $name,
        Code $code,
        Domain $domain,
        Description $description,
        Color $color,
        bool $active,
        LocalesConfig $locales,
        Environments $environments,
    ): self {
        $event = self::occur($projectId->toString(), [
            'cookie_provider_id' => $cookieProviderId->toString(),
            'name' => $name->value(),
            'code' => $code->value(),
            'domain' => $domain->value(),
            'description' => $description->value(),
            'color' => $color->value(),
            'active' => $active,
            'locales' => $locales->locales()->toArray(),
            'default_locale' => $locales->defaultLocale()->value(),
            'environments' => $environments->toArray(),
        ]);

        $event->projectId = $projectId;
        $event->cookieProviderId = $cookieProviderId;
        $event->name = $name;
        $event->code = $code;
        $event->domain = $domain;
        $event->description = $description;
        $event->color = $color;
        $event->active = $active;
        $event->locales = $locales;
        $event->environments = $environments;

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

    public function name(): Name
    {
        return $this->name;
    }

    public function code(): Code
    {
        return $this->code;
    }

    public function domain(): Domain
    {
        return $this->domain;
    }

    public function description(): Description
    {
        return $this->description;
    }

    public function color(): Color
    {
        return $this->color;
    }

    public function active(): bool
    {
        return $this->active;
    }

    public function locales(): LocalesConfig
    {
        return $this->locales;
    }

    public function environments(): Environments
    {
        return $this->environments;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
        $this->cookieProviderId = CookieProviderId::fromString($parameters['cookie_provider_id']);
        $this->name = Name::fromValue($parameters['name']);
        $this->code = Code::fromValue($parameters['code']);
        $this->domain = Domain::fromValue($parameters['domain'] ?? $parameters['code']); # fallback to code for back compatibility
        $this->description = Description::fromValue($parameters['description']);
        $this->color = Color::fromValue($parameters['color']);
        $this->active = (bool) $parameters['active'];
        $this->locales = LocalesConfig::create(Locales::reconstitute($parameters['locales']), Locale::fromValue($parameters['default_locale']));
        $this->environments = Environments::reconstitute($parameters['environments'] ?? []);
    }
}
