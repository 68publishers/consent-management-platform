<?php

declare(strict_types=1);

namespace App\Domain\Project;

use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\Project\Command\CreateProjectCommand;
use App\Domain\Project\Command\UpdateProjectCommand;
use App\Domain\Project\Event\ProjectActiveStateChanged;
use App\Domain\Project\Event\ProjectCodeChanged;
use App\Domain\Project\Event\ProjectColorChanged;
use App\Domain\Project\Event\ProjectCookieProviderAdded;
use App\Domain\Project\Event\ProjectCookieProviderRemoved;
use App\Domain\Project\Event\ProjectCreated;
use App\Domain\Project\Event\ProjectDescriptionChanged;
use App\Domain\Project\Event\ProjectDomainChanged;
use App\Domain\Project\Event\ProjectEnvironmentsChanged;
use App\Domain\Project\Event\ProjectLocalesChanged;
use App\Domain\Project\Event\ProjectNameChanged;
use App\Domain\Project\Event\ProjectTemplateChanged;
use App\Domain\Project\ValueObject\Code;
use App\Domain\Project\ValueObject\Color;
use App\Domain\Project\ValueObject\Description;
use App\Domain\Project\ValueObject\Domain;
use App\Domain\Project\ValueObject\Environment;
use App\Domain\Project\ValueObject\Environments;
use App\Domain\Project\ValueObject\Name;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Project\ValueObject\Template;
use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Shared\ValueObject\Locales;
use App\Domain\Shared\ValueObject\LocalesConfig;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootInterface;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\DeletableAggregateRootTrait;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;

final class Project implements AggregateRootInterface
{
    use DeletableAggregateRootTrait;

    private ProjectId $id;

    private CookieProviderId $cookieProviderId;

    private DateTimeImmutable $createdAt;

    private Name $name;

    private Code $code;

    private Domain $domain;

    private Color $color;

    private Description $description;

    private bool $active;

    private LocalesConfig $locales;

    private Environments $environments;

    /** @var Collection<ProjectHasCookieProvider>  */
    private Collection $cookieProviders;

    /** @var Collection<ProjectTranslation>  */
    private Collection $translations;

    public static function create(CreateProjectCommand $command, CheckCodeUniquenessInterface $checkCodeUniqueness): self
    {
        $project = new self();

        $projectId = null !== $command->projectId() ? ProjectId::fromString($command->projectId()) : ProjectId::new();
        $cookieProviderId = null !== $command->cookieProviderId() ? CookieProviderId::fromString($command->cookieProviderId()) : CookieProviderId::new();
        $name = Name::fromValue($command->name());
        $code = Code::fromValidCode($command->code());
        $domain = Domain::fromValue($command->domain());
        $description = Description::fromValue($command->description());
        $color = Color::fromValidColor($command->color());
        $locales = Locales::empty();
        $defaultLocale = Locale::fromValue($command->defaultLocale());
        $environments = Environments::empty();

        foreach ($command->locales() as $locale) {
            $locales = $locales->with(Locale::fromValue($locale));
        }

        foreach ($command->environments() as $environment) {
            $environments = $environments->with(Environment::fromValue($environment));
        }

        $checkCodeUniqueness($projectId, $code);

        $project->recordThat(ProjectCreated::create(
            projectId: $projectId,
            cookieProviderId: $cookieProviderId,
            name: $name,
            code: $code,
            domain: $domain,
            description: $description,
            color: $color,
            active: $command->active(),
            locales: LocalesConfig::create($locales, $defaultLocale),
            environments: $environments,
        ));

        $project->setCookieProviders(array_map(
            static fn (string $cookieProviderId): CookieProviderId => CookieProviderId::fromString($cookieProviderId),
            $command->cookieProviderIds(),
        ));

        return $project;
    }

    public function update(UpdateProjectCommand $command, CheckCodeUniquenessInterface $checkCodeUniqueness): void
    {
        if (null !== $command->name()) {
            $this->changeName(Name::fromValue($command->name()));
        }

        if (null !== $command->code()) {
            $this->changeCode(Code::fromValidCode($command->code()), $checkCodeUniqueness);
        }

        if (null !== $command->domain()) {
            $this->changeDomain(Domain::fromValue($command->domain()));
        }

        if (null !== $command->color()) {
            $this->changeColor(Color::fromValidColor($command->color()));
        }

        if (null !== $command->description()) {
            $this->changeDescription(Description::fromValue($command->description()));
        }

        if (null !== $command->active()) {
            $this->changeActiveState($command->active());
        }

        if (null !== $command->locales() && null !== $command->defaultLocale()) {
            $locales = Locales::empty();
            $defaultLocale = Locale::fromValue($command->defaultLocale());

            foreach ($command->locales() as $locale) {
                $locales = $locales->with(Locale::fromValue($locale));
            }

            $this->changeLocales(LocalesConfig::create($locales, $defaultLocale));
        }

        if (null !== $command->environments()) {
            $environments = Environments::empty();

            foreach ($command->environments() as $environment) {
                $environments = $environments->with(Environment::fromValue($environment));
            }

            $this->changeEnvironments($environments);
        }

        if (null !== $command->cookieProviderIds()) {
            $this->setCookieProviders(array_map(static fn (string $cookieProviderId): CookieProviderId => CookieProviderId::fromString($cookieProviderId), $command->cookieProviderIds()));
        }
    }

    public function aggregateId(): AggregateId
    {
        return AggregateId::fromUuid($this->id->id());
    }

    public function changeName(Name $name): void
    {
        if (!$this->name->equals($name)) {
            $this->recordThat(ProjectNameChanged::create($this->id, $name));
        }
    }

    public function changeCode(Code $code, CheckCodeUniquenessInterface $checkCodeUniqueness): void
    {
        $code = Code::fromValidCode($code->value());

        if (!$this->code->equals($code)) {
            $checkCodeUniqueness($this->id, $code);
            $this->recordThat(ProjectCodeChanged::create($this->id, $code));
        }
    }

    public function changeDomain(Domain $domain): void
    {
        if (!$this->domain->equals($domain)) {
            $this->recordThat(ProjectDomainChanged::create($this->id, $domain));
        }
    }

    public function changeColor(Color $color): void
    {
        $color = Color::fromValidColor($color->value());

        if (!$this->color->equals($color)) {
            $this->recordThat(ProjectColorChanged::create($this->id, $color));
        }
    }

    public function changeDescription(Description $description): void
    {
        if (!$this->description->equals($description)) {
            $this->recordThat(ProjectDescriptionChanged::create($this->id, $description));
        }
    }

    public function changeActiveState(bool $active): void
    {
        if ($this->active !== $active) {
            $this->recordThat(ProjectActiveStateChanged::create($this->id, $active));
        }
    }

    public function changeLocales(LocalesConfig $locales): void
    {
        if (!$this->locales->equals($locales)) {
            $this->recordThat(ProjectLocalesChanged::create($this->id, $locales));
        }
    }

    public function changeEnvironments(Environments $environments): void
    {
        if (!$this->environments->equals($environments)) {
            $this->recordThat(ProjectEnvironmentsChanged::create($this->id, $environments));
        }
    }

    /**
     * @param array<CookieProviderId> $cookieProviderIds
     */
    public function setCookieProviders(array $cookieProviderIds): void
    {
        foreach ($this->cookieProviders as $projectHasCookieProvider) {
            if (!$this->hasCookieProvider($cookieProviderIds, $projectHasCookieProvider->cookieProviderId())) {
                $this->recordThat(ProjectCookieProviderRemoved::create($this->id, $projectHasCookieProvider->cookieProviderId()));
            }
        }

        foreach ($cookieProviderIds as $cookieProviderId) {
            $this->addCookieProvider($cookieProviderId);
        }
    }

    public function addCookieProvider(CookieProviderId $cookieProviderId): void
    {
        if (!$this->hasCookieProvider($this->cookieProviders, $cookieProviderId)) {
            $this->recordThat(ProjectCookieProviderAdded::create($this->id, $cookieProviderId));
        }
    }

    public function removeCookieProvider(CookieProviderId $cookieProviderId): void
    {
        if ($this->hasCookieProvider($this->cookieProviders, $cookieProviderId)) {
            $this->recordThat(ProjectCookieProviderRemoved::create($this->id, $cookieProviderId));
        }
    }

    public function changeTemplate(Locale $locale, Template $template, TemplateValidatorInterface $templateValidator): void
    {
        $translation = $this->translations->filter(static fn (ProjectTranslation $translation): bool => $translation->locale()->equals($locale))->first();

        if (!$translation instanceof ProjectTranslation || !$translation->template()->equals($template)) {
            $templateValidator($this->id, $template, $locale);

            $this->recordThat(ProjectTemplateChanged::create($this->id, $template, $locale));
        }
    }

    protected function whenProjectCreated(ProjectCreated $event): void
    {
        $this->id = $event->projectId();
        $this->cookieProviderId = $event->cookieProviderId();
        $this->createdAt = $event->createdAt();
        $this->name = $event->name();
        $this->code = $event->code();
        $this->domain = $event->domain();
        $this->color = $event->color();
        $this->description = $event->description();
        $this->active = $event->active();
        $this->locales = $event->locales();
        $this->environments = $event->environments();
        $this->cookieProviders = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    protected function whenProjectNameChanged(ProjectNameChanged $event): void
    {
        $this->name = $event->name();
    }

    protected function whenProjectCodeChanged(ProjectCodeChanged $event): void
    {
        $this->code = $event->code();
    }

    protected function whenProjectDomainChanged(ProjectDomainChanged $event): void
    {
        $this->domain = $event->domain();
    }

    protected function whenProjectColorChanged(ProjectColorChanged $event): void
    {
        $this->color = $event->color();
    }

    protected function whenProjectDescriptionChanged(ProjectDescriptionChanged $event): void
    {
        $this->description = $event->description();
    }

    protected function whenProjectActiveStateChanged(ProjectActiveStateChanged $event): void
    {
        $this->active = $event->active();
    }

    protected function whenProjectLocalesChanged(ProjectLocalesChanged $event): void
    {
        $this->locales = $event->locales();
    }

    protected function WhenProjectEnvironmentsChanged(ProjectEnvironmentsChanged $event): void
    {
        $this->environments = $event->environments();
    }

    protected function whenProjectCookieProviderAdded(ProjectCookieProviderAdded $event): void
    {
        $this->cookieProviders->add(ProjectHasCookieProvider::create($this, $event->cookieProviderId()));
    }

    protected function whenProjectCookieProviderRemoved(ProjectCookieProviderRemoved $event): void
    {
        $projectHasCookieProvider = $this->hasCookieProvider($this->cookieProviders, $event->cookieProviderId());

        if ($projectHasCookieProvider instanceof ProjectHasCookieProvider) {
            $this->cookieProviders->removeElement($projectHasCookieProvider);
        }
    }

    protected function whenProjectTemplateChanged(ProjectTemplateChanged $event): void
    {
        $translation = $this->translations->filter(static fn (ProjectTranslation $translation): bool => $translation->locale()->equals($event->locale()))->first();

        if ($translation instanceof ProjectTranslation) {
            $translation->setTemplate($event->template());

            return;
        }

        $this->translations->add(ProjectTranslation::create($this, $event->locale(), $event->template()));
    }

    /**
     * @param iterable<CookieProviderId|ProjectHasCookieProvider> $collection
     *
     * @return CookieProviderId|ProjectHasCookieProvider|FALSE
     */
    private function hasCookieProvider(iterable $collection, CookieProviderId $cookieProviderId): CookieProviderId|ProjectHasCookieProvider|bool
    {
        foreach ($collection as $item) {
            $id = $item;

            if ($item instanceof ProjectHasCookieProvider) {
                $id = $item->cookieProviderId();
            }

            if ($id->equals($cookieProviderId)) {
                return $item;
            }
        }

        return false;
    }
}
