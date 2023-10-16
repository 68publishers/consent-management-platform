<?php

declare(strict_types=1);

namespace App\Domain\Cookie;

use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Cookie\Command\CreateCookieCommand;
use App\Domain\Cookie\Command\UpdateCookieCommand;
use App\Domain\Cookie\Event\CookieActiveStateChanged;
use App\Domain\Cookie\Event\CookieCategoryChanged;
use App\Domain\Cookie\Event\CookieCreated;
use App\Domain\Cookie\Event\CookieDomainChanged;
use App\Domain\Cookie\Event\CookieEnvironmentsChanged;
use App\Domain\Cookie\Event\CookieNameChanged;
use App\Domain\Cookie\Event\CookieProcessingTimeChanged;
use App\Domain\Cookie\Event\CookiePurposeChanged;
use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Cookie\ValueObject\Domain;
use App\Domain\Cookie\ValueObject\Environment;
use App\Domain\Cookie\ValueObject\Environments;
use App\Domain\Cookie\ValueObject\Name;
use App\Domain\Cookie\ValueObject\ProcessingTime;
use App\Domain\Cookie\ValueObject\Purpose;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\Shared\ValueObject\Locale;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootInterface;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\DeletableAggregateRootTrait;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;

final class Cookie implements AggregateRootInterface
{
    use DeletableAggregateRootTrait;

    private CookieId $id;

    private CategoryId $categoryId;

    private CookieProviderId $cookieProviderId;

    private DateTimeImmutable $createdAt;

    private Name $name;

    private Domain $domain;

    private ProcessingTime $processingTime;

    private bool $active;

    private bool $allEnvironments;

    private Environments $environments;

    private Collection $translations;

    public static function create(CreateCookieCommand $command, CheckCategoryExistsInterface $checkCategoryExists, CheckCookieProviderExistsInterface $checkCookieProviderExists, CheckNameUniquenessInterface $checkNameUniqueness): self
    {
        $cookie = new self();

        $id = null !== $command->cookieId() ? CookieId::fromString($command->cookieId()) : CookieId::new();
        $categoryId = CategoryId::fromString($command->categoryId());
        $cookieProviderId = CookieProviderId::fromString($command->cookieProviderId());
        $name = Name::fromValue($command->name());
        $domain = Domain::fromValue($command->domain());
        $processingTime = ProcessingTime::withValidation($command->processingTime());
        $active = $command->active();
        $purposes = array_map(static fn (string $purpose): Purpose => Purpose::fromValue($purpose), $command->purposes());
        $allEnvironments = true === $command->environments();
        $environments = Environments::empty();

        foreach (is_array($command->environments()) ? $command->environments() : [] as $environment) {
            $environments = $environments->with(Environment::fromValue($environment));
        }

        $checkCategoryExists($categoryId);
        $checkCookieProviderExists($cookieProviderId);
        $checkNameUniqueness($id, $name, $cookieProviderId, $categoryId);

        $cookie->recordThat(CookieCreated::create(
            cookieId: $id,
            categoryId: $categoryId,
            cookieProviderId: $cookieProviderId,
            name: $name,
            domain: $domain,
            processingTime: $processingTime,
            active: $active,
            purposes: $purposes,
            allEnvironments: $allEnvironments,
            environments: $environments,
        ));

        return $cookie;
    }

    public function update(UpdateCookieCommand $command, CheckCategoryExistsInterface $checkCategoryExists, CheckNameUniquenessInterface $checkNameUniqueness): void
    {
        if (null !== $command->categoryId()) {
            $this->changeCategoryId(CategoryId::fromString($command->categoryId()), $checkCategoryExists);
        }

        if (null !== $command->categoryId() || null !== $command->name()) {
            $checkNameUniqueness(
                $this->id,
                null !== $command->name() ? Name::fromValue($command->name()) : $this->name,
                $this->cookieProviderId,
                null !== $command->categoryId() ? CategoryId::fromString($command->categoryId()) : $this->categoryId,
            );
        }

        if (null !== $command->name()) {
            $name = Name::fromValue($command->name());

            if (!$this->name->equals($name)) {
                $this->recordThat(CookieNameChanged::create($this->id, $name));
            }
        }

        if (null !== $command->domain()) {
            $this->changeDomain(Domain::fromValue($command->domain()));
        }

        if (null !== $command->processingTime()) {
            $this->changeProcessingTime(ProcessingTime::withValidation($command->processingTime()));
        }

        if (null !== $command->active()) {
            $this->changeActiveState($command->active());
        }

        if (null !== $command->purposes()) {
            foreach ($command->purposes() as $locale => $purpose) {
                $this->changePurpose(Locale::fromValue($locale), Purpose::fromValue($purpose));
            }
        }

        if (null !== $command->environments()) {
            $allEnvironments = true === $command->environments();
            $environments = Environments::empty();

            foreach (is_array($command->environments()) ? $command->environments() : [] as $environment) {
                $environments = $environments->with(Environment::fromValue($environment));
            }

            if ($allEnvironments !== $this->allEnvironments || !$this->environments->equals($environments)) {
                $this->recordThat(CookieEnvironmentsChanged::create($this->id, $allEnvironments, $environments));
            }
        }
    }

    public function aggregateId(): AggregateId
    {
        return AggregateId::fromUuid($this->id->id());
    }

    public function changeCategoryId(CategoryId $categoryId, CheckCategoryExistsInterface $checkCategoryExists): void
    {
        if (!$this->categoryId->equals($categoryId)) {
            $checkCategoryExists($categoryId);
            $this->recordThat(CookieCategoryChanged::create($this->id, $categoryId));
        }
    }

    public function changeDomain(Domain $domain): void
    {
        if (!$this->domain->equals($domain)) {
            $this->recordThat(CookieDomainChanged::create($this->id, $domain));
        }
    }

    public function changePurpose(Locale $locale, Purpose $purpose): void
    {
        $translation = $this->translations->filter(static fn (CookieTranslation $translation): bool => $translation->locale()->equals($locale))->first();

        if (!$translation instanceof CookieTranslation || !$translation->purpose()->equals($purpose)) {
            $this->recordThat(CookiePurposeChanged::create($this->id, $locale, $purpose));
        }
    }

    public function changeProcessingTime(ProcessingTime $processingTime): void
    {
        if (!$this->processingTime->equals($processingTime)) {
            $this->recordThat(CookieProcessingTimeChanged::create($this->id, $processingTime));
        }
    }

    public function changeActiveState(bool $active): void
    {
        if ($this->active !== $active) {
            $this->recordThat(CookieActiveStateChanged::create($this->id, $active));
        }
    }

    protected function whenCookieCreated(CookieCreated $event): void
    {
        $this->id = $event->cookieId();
        $this->categoryId = $event->categoryId();
        $this->cookieProviderId = $event->cookieProviderId();
        $this->createdAt = $event->createdAt();
        $this->name = $event->name();
        $this->domain = $event->domain();
        $this->processingTime = $event->processingTime();
        $this->active = $event->active();
        $this->allEnvironments = $event->allEnvironments();
        $this->environments = $event->environments();
        $this->translations = new ArrayCollection();

        foreach ($event->purposes() as $locale => $purpose) {
            $this->translations->add(CookieTranslation::create($this, Locale::fromValue($locale), $purpose));
        }
    }

    protected function whenCookieCategoryChanged(CookieCategoryChanged $event): void
    {
        $this->categoryId = $event->categoryId();
    }

    protected function whenCookieNameChanged(CookieNameChanged $event): void
    {
        $this->name = $event->name();
    }

    protected function whenCookieDomainChanged(CookieDomainChanged $event): void
    {
        $this->domain = $event->domain();
    }

    protected function whenCookieProcessingTimeChanged(CookieProcessingTimeChanged $event): void
    {
        $this->processingTime = $event->processingTime();
    }

    protected function whenCookieActiveStateChanged(CookieActiveStateChanged $event): void
    {
        $this->active = $event->active();
    }

    protected function whenCookiePurposeChanged(CookiePurposeChanged $event): void
    {
        $translation = $this->filterTranslation($event->locale());

        if ($translation instanceof CookieTranslation) {
            $translation->setPurpose($event->purpose());

            return;
        }

        $this->translations->add(CookieTranslation::create($this, $event->locale(), $event->purpose()));
    }

    protected function whenCookieEnvironmentsChanged(CookieEnvironmentsChanged $event): void
    {
        $this->allEnvironments = $event->allEnvironments();
        $this->environments = $event->environments();
    }

    private function filterTranslation(Locale $locale): ?CookieTranslation
    {
        $translation = $this->translations->filter(static fn (CookieTranslation $translation): bool => $translation->locale()->equals($locale))->first();

        return $translation ?: null;
    }
}
