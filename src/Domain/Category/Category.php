<?php

declare(strict_types=1);

namespace App\Domain\Category;

use App\Domain\Category\Command\CreateCategoryCommand;
use App\Domain\Category\Command\UpdateCategoryCommand;
use App\Domain\Category\Event\CategoryActiveStateChanged;
use App\Domain\Category\Event\CategoryCodeChanged;
use App\Domain\Category\Event\CategoryCreated;
use App\Domain\Category\Event\CategoryNameUpdated;
use App\Domain\Category\Event\CategoryNecessaryChanged;
use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Category\ValueObject\Code;
use App\Domain\Category\ValueObject\Name;
use App\Domain\Shared\ValueObject\Locale;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootInterface;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\DeletableAggregateRootTrait;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;

final class Category implements AggregateRootInterface
{
    use DeletableAggregateRootTrait;

    private CategoryId $id;

    private DateTimeImmutable $createdAt;

    private Code $code;

    private bool $active;

    private bool $necessary;

    private Collection $translations;

    public static function create(CreateCategoryCommand $command, CheckCodeUniquenessInterface $checkCodeUniqueness): self
    {
        $category = new self();

        $categoryId = null !== $command->categoryId() ? CategoryId::fromString($command->categoryId()) : CategoryId::new();
        $code = Code::fromValidCode($command->code());
        $active = $command->active();
        $necessary = $command->necessary();
        $names = $command->names();

        $checkCodeUniqueness($categoryId, $code);

        $category->recordThat(CategoryCreated::create($categoryId, $code, $active, $necessary, $names));

        return $category;
    }

    public function update(UpdateCategoryCommand $command, CheckCodeUniquenessInterface $checkCodeUniqueness): void
    {
        if (null !== $command->code()) {
            $this->changeCode(Code::fromValidCode($command->code()), $checkCodeUniqueness);
        }

        if (null !== $command->active()) {
            $this->changeActiveState($command->active());
        }

        if (null !== $command->necessary()) {
            $this->changeNecessary($command->necessary());
        }

        if (null !== $command->names()) {
            foreach ($command->names() as $locale => $name) {
                $this->changeName(Locale::fromValue($locale), Name::fromValue($name));
            }
        }
    }

    public function aggregateId(): AggregateId
    {
        return AggregateId::fromUuid($this->id->id());
    }

    public function changeCode(Code $code, CheckCodeUniquenessInterface $checkCodeUniqueness): void
    {
        if (!$this->code->equals($code)) {
            $checkCodeUniqueness($this->id, $code);

            $this->recordThat(CategoryCodeChanged::create($this->id, $code));
        }
    }

    public function changeActiveState(bool $active): void
    {
        if ($this->active !== $active) {
            $this->recordThat(CategoryActiveStateChanged::create($this->id, $active));
        }
    }

    public function changeNecessary(bool $necessary): void
    {
        if ($this->necessary !== $necessary) {
            $this->recordThat(CategoryNecessaryChanged::create($this->id, $necessary));
        }
    }

    public function changeName(Locale $locale, Name $name): void
    {
        $translation = $this->translations->filter(static fn (CategoryTranslation $translation): bool => $translation->locale()->equals($locale))->first();

        if (!$translation instanceof CategoryTranslation || !$translation->name()->equals($name)) {
            $this->recordThat(CategoryNameUpdated::create($this->id, $locale, $name));
        }
    }

    protected function whenCategoryCreated(CategoryCreated $event): void
    {
        $this->id = $event->categoryId();
        $this->createdAt = $event->createdAt();
        $this->code = $event->code();
        $this->active = $event->active();
        $this->necessary = $event->necessary();
        $this->translations = new ArrayCollection();

        foreach ($event->names() as $locale => $name) {
            $this->translations->add(CategoryTranslation::create($this, Locale::fromValue($locale), Name::fromValue($name)));
        }
    }

    protected function whenCategoryCodeChanged(CategoryCodeChanged $event): void
    {
        $this->code = $event->code();
    }

    protected function whenCategoryActiveStateChanged(CategoryActiveStateChanged $event): void
    {
        $this->active = $event->active();
    }

    protected function whenCategoryNecessaryChanged(CategoryNecessaryChanged $event): void
    {
        $this->necessary = $event->necessary();
    }

    protected function whenCategoryNameUpdated(CategoryNameUpdated $event): void
    {
        $translation = $this->translations->filter(static fn (CategoryTranslation $translation): bool => $translation->locale()->equals($event->locale()))->first();

        if ($translation instanceof CategoryTranslation) {
            $translation->setName($event->name());

            return;
        }

        $this->translations->add(CategoryTranslation::create($this, $event->locale(), $event->name()));
    }
}
