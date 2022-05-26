<?php

declare(strict_types=1);

namespace App\Domain\Category;

use DateTimeImmutable;
use App\Domain\Category\ValueObject\Code;
use App\Domain\Category\ValueObject\Name;
use App\Domain\Shared\ValueObject\Locale;
use Doctrine\Common\Collections\Collection;
use App\Domain\Category\Event\CategoryCreated;
use App\Domain\Category\ValueObject\CategoryId;
use Doctrine\Common\Collections\ArrayCollection;
use App\Domain\Category\Event\CategoryCodeChanged;
use App\Domain\Category\Event\CategoryNameUpdated;
use App\Domain\Category\Command\CreateCategoryCommand;
use App\Domain\Category\Command\UpdateCategoryCommand;
use App\Domain\Category\Event\CategoryActiveStateChanged;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootInterface;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\DeletableAggregateRootTrait;

final class Category implements AggregateRootInterface
{
	use DeletableAggregateRootTrait;

	private CategoryId $id;

	private DateTimeImmutable $createdAt;

	private Code $code;

	private bool $active;

	private Collection $translations;

	/**
	 * @param \App\Domain\Category\Command\CreateCategoryCommand $command
	 * @param \App\Domain\Category\CheckCodeUniquenessInterface  $checkCodeUniqueness
	 *
	 * @return static
	 */
	public static function create(CreateCategoryCommand $command, CheckCodeUniquenessInterface $checkCodeUniqueness): self
	{
		$category = new self();

		$categoryId = NULL !== $command->categoryId() ? CategoryId::fromString($command->categoryId()) : CategoryId::new();
		$code = Code::fromValidCode($command->code());
		$active = $command->active();
		$names = $command->names();

		$checkCodeUniqueness($categoryId, $code);

		$category->recordThat(CategoryCreated::create($categoryId, $code, $active, $names));

		return $category;
	}

	/**
	 * @param \App\Domain\Category\Command\UpdateCategoryCommand $command
	 * @param \App\Domain\Category\CheckCodeUniquenessInterface  $checkCodeUniqueness
	 *
	 * @return void
	 */
	public function update(UpdateCategoryCommand $command, CheckCodeUniquenessInterface $checkCodeUniqueness): void
	{
		if (NULL !== $command->code()) {
			$this->changeCode(Code::fromValidCode($command->code()), $checkCodeUniqueness);
		}

		if (NULL !== $command->active()) {
			$this->changeActiveState($command->active());
		}

		if (NULL !== $command->names()) {
			foreach ($command->names() as $locale => $name) {
				$this->changeName(Locale::fromValue($locale), Name::fromValue($name));
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function aggregateId(): AggregateId
	{
		return AggregateId::fromUuid($this->id->id());
	}

	/**
	 * @param \App\Domain\Category\ValueObject\Code             $code
	 * @param \App\Domain\Category\CheckCodeUniquenessInterface $checkCodeUniqueness
	 *
	 * @return void
	 */
	public function changeCode(Code $code, CheckCodeUniquenessInterface $checkCodeUniqueness): void
	{
		if (!$this->code->equals($code)) {
			$checkCodeUniqueness($this->id, $code);

			$this->recordThat(CategoryCodeChanged::create($this->id, $code));
		}
	}

	/**
	 * @param bool $active
	 *
	 * @return void
	 */
	public function changeActiveState(bool $active): void
	{
		if ($this->active !== $active) {
			$this->recordThat(CategoryActiveStateChanged::create($this->id, $active));
		}
	}

	/**
	 * @param \App\Domain\Shared\ValueObject\Locale $locale
	 * @param \App\Domain\Category\ValueObject\Name $name
	 *
	 * @return void
	 */
	public function changeName(Locale $locale, Name $name): void
	{
		$translation = $this->translations->filter(static fn (CategoryTranslation $translation): bool => $translation->locale()->equals($locale))->first();

		if (!$translation instanceof CategoryTranslation || !$translation->name()->equals($name)) {
			$this->recordThat(CategoryNameUpdated::create($this->id, $locale, $name));
		}
	}

	/**
	 * @param \App\Domain\Category\Event\CategoryCreated $event
	 *
	 * @return void
	 */
	protected function whenCategoryCreated(CategoryCreated $event): void
	{
		$this->id = $event->categoryId();
		$this->createdAt = $event->createdAt();
		$this->code = $event->code();
		$this->active = $event->active();
		$this->translations = new ArrayCollection();

		foreach ($event->names() as $locale => $name) {
			$this->translations->add(CategoryTranslation::create($this, Locale::fromValue($locale), Name::fromValue($name)));
		}
	}

	/**
	 * @param \App\Domain\Category\Event\CategoryCodeChanged $event
	 *
	 * @return void
	 */
	protected function whenCategoryCodeChanged(CategoryCodeChanged $event): void
	{
		$this->code = $event->code();
	}

	/**
	 * @param \App\Domain\Category\Event\CategoryActiveStateChanged $event
	 *
	 * @return void
	 */
	protected function whenCategoryActiveStateChanged(CategoryActiveStateChanged $event): void
	{
		$this->active = $event->active();
	}

	/**
	 * @param \App\Domain\Category\Event\CategoryNameUpdated $event
	 *
	 * @return void
	 */
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
