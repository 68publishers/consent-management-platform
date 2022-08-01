<?php

declare(strict_types=1);

namespace App\Domain\Cookie;

use DateTimeImmutable;
use App\Domain\Cookie\ValueObject\Name;
use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Cookie\Event\CookieCreated;
use App\Domain\Cookie\ValueObject\Purpose;
use App\Domain\Cookie\ValueObject\CookieId;
use Doctrine\Common\Collections\Collection;
use App\Domain\Cookie\Event\CookieNameChanged;
use App\Domain\Category\ValueObject\CategoryId;
use Doctrine\Common\Collections\ArrayCollection;
use App\Domain\Cookie\Event\CookiePurposeChanged;
use App\Domain\Cookie\ValueObject\ProcessingTime;
use App\Domain\Cookie\Command\CreateCookieCommand;
use App\Domain\Cookie\Command\UpdateCookieCommand;
use App\Domain\Cookie\Event\CookieCategoryChanged;
use App\Domain\Cookie\Event\CookieActiveStateChanged;
use App\Domain\Cookie\Event\CookieProcessingTimeChanged;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootInterface;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\DeletableAggregateRootTrait;

final class Cookie implements AggregateRootInterface
{
	use DeletableAggregateRootTrait;

	private CookieId $id;

	private CategoryId $categoryId;

	private CookieProviderId $cookieProviderId;

	private DateTimeImmutable $createdAt;

	private Name $name;

	private ProcessingTime $processingTime;

	private bool $active;

	private Collection $translations;

	/**
	 * @param \App\Domain\Cookie\Command\CreateCookieCommand        $command
	 * @param \App\Domain\Cookie\CheckCategoryExistsInterface       $checkCategoryExists
	 * @param \App\Domain\Cookie\CheckCookieProviderExistsInterface $checkCookieProviderExists
	 * @param \App\Domain\Cookie\CheckNameUniquenessInterface       $checkNameUniqueness
	 *
	 * @return static
	 */
	public static function create(CreateCookieCommand $command, CheckCategoryExistsInterface $checkCategoryExists, CheckCookieProviderExistsInterface $checkCookieProviderExists, CheckNameUniquenessInterface $checkNameUniqueness): self
	{
		$cookie = new self();

		$id = NULL !== $command->cookieId() ? CookieId::fromString($command->cookieId()) : CookieId::new();
		$categoryId = CategoryId::fromString($command->categoryId());
		$cookieProviderId = CookieProviderId::fromString($command->cookieProviderId());
		$name = Name::fromValue($command->name());
		$processingTime = ProcessingTime::withValidation($command->processingTime());
		$active = $command->active();
		$purposes = array_map(static fn (string $purpose): Purpose => Purpose::fromValue($purpose), $command->purposes());

		$checkCategoryExists($categoryId);
		$checkCookieProviderExists($cookieProviderId);
		$checkNameUniqueness($id, $name, $cookieProviderId);

		$cookie->recordThat(CookieCreated::create($id, $categoryId, $cookieProviderId, $name, $processingTime, $active, $purposes));

		return $cookie;
	}

	/**
	 * @param \App\Domain\Cookie\Command\UpdateCookieCommand  $command
	 * @param \App\Domain\Cookie\CheckCategoryExistsInterface $checkCategoryExists
	 * @param \App\Domain\Cookie\CheckNameUniquenessInterface $checkNameUniqueness
	 *
	 * @return void
	 */
	public function update(UpdateCookieCommand $command, CheckCategoryExistsInterface $checkCategoryExists, CheckNameUniquenessInterface $checkNameUniqueness): void
	{
		if (NULL !== $command->categoryId()) {
			$this->changeCategoryId(CategoryId::fromString($command->categoryId()), $checkCategoryExists);
		}

		if (NULL !== $command->name()) {
			$this->changeName(Name::fromValue($command->name()), $checkNameUniqueness);
		}

		if (NULL !== $command->processingTime()) {
			$this->changeProcessingTime(ProcessingTime::withValidation($command->processingTime()));
		}

		if (NULL !== $command->active()) {
			$this->changeActiveState($command->active());
		}

		if (NULL !== $command->purposes()) {
			foreach ($command->purposes() as $locale => $purpose) {
				$this->changePurpose(Locale::fromValue($locale), Purpose::fromValue($purpose));
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
	 * @param \App\Domain\Category\ValueObject\CategoryId     $categoryId
	 * @param \App\Domain\Cookie\CheckCategoryExistsInterface $checkCategoryExists
	 *
	 * @return void
	 */
	public function changeCategoryId(CategoryId $categoryId, CheckCategoryExistsInterface $checkCategoryExists): void
	{
		if (!$this->categoryId->equals($categoryId)) {
			$checkCategoryExists($categoryId);
			$this->recordThat(CookieCategoryChanged::create($this->id, $categoryId));
		}
	}

	/**
	 * @param \App\Domain\Cookie\ValueObject\Name             $name
	 * @param \App\Domain\Cookie\CheckNameUniquenessInterface $checkNameUniqueness
	 *
	 * @return void
	 */
	public function changeName(Name $name, CheckNameUniquenessInterface $checkNameUniqueness): void
	{
		if (!$this->name->equals($name)) {
			$checkNameUniqueness($this->id, $name, $this->cookieProviderId);
			$this->recordThat(CookieNameChanged::create($this->id, $name));
		}
	}

	/**
	 * @param \App\Domain\Shared\ValueObject\Locale  $locale
	 * @param \App\Domain\Cookie\ValueObject\Purpose $purpose
	 *
	 * @return void
	 */
	public function changePurpose(Locale $locale, Purpose $purpose): void
	{
		$translation = $this->translations->filter(static fn (CookieTranslation $translation): bool => $translation->locale()->equals($locale))->first();

		if (!$translation instanceof CookieTranslation || !$translation->purpose()->equals($purpose)) {
			$this->recordThat(CookiePurposeChanged::create($this->id, $locale, $purpose));
		}
	}

	/**
	 * @param \App\Domain\Cookie\ValueObject\ProcessingTime $processingTime
	 *
	 * @return void
	 */
	public function changeProcessingTime(ProcessingTime $processingTime): void
	{
		if (!$this->processingTime->equals($processingTime)) {
			$this->recordThat(CookieProcessingTimeChanged::create($this->id, $processingTime));
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
			$this->recordThat(CookieActiveStateChanged::create($this->id, $active));
		}
	}

	/**
	 * @param \App\Domain\Cookie\Event\CookieCreated $event
	 *
	 * @return void
	 */
	protected function whenCookieCreated(CookieCreated $event): void
	{
		$this->id = $event->cookieId();
		$this->categoryId = $event->categoryId();
		$this->cookieProviderId = $event->cookieProviderId();
		$this->createdAt = $event->createdAt();
		$this->name = $event->name();
		$this->processingTime = $event->processingTime();
		$this->active = $event->active();
		$this->translations = new ArrayCollection();

		foreach ($event->purposes() as $locale => $purpose) {
			$this->translations->add(CookieTranslation::create($this, Locale::fromValue($locale), $purpose));
		}
	}

	/**
	 * @param \App\Domain\Cookie\Event\CookieCategoryChanged $event
	 *
	 * @return void
	 */
	protected function whenCookieCategoryChanged(CookieCategoryChanged $event): void
	{
		$this->categoryId = $event->categoryId();
	}

	/**
	 * @param \App\Domain\Cookie\Event\CookieNameChanged $event
	 *
	 * @return void
	 */
	protected function whenCookieNameChanged(CookieNameChanged $event): void
	{
		$this->name = $event->name();
	}

	/**
	 * @param \App\Domain\Cookie\Event\CookieProcessingTimeChanged $event
	 *
	 * @return void
	 */
	protected function whenCookieProcessingTimeChanged(CookieProcessingTimeChanged $event): void
	{
		$this->processingTime = $event->processingTime();
	}

	/**
	 * @param \App\Domain\Cookie\Event\CookieActiveStateChanged $event
	 *
	 * @return void
	 */
	protected function whenCookieActiveStateChanged(CookieActiveStateChanged $event): void
	{
		$this->active = $event->active();
	}

	/**
	 * @param \App\Domain\Cookie\Event\CookiePurposeChanged $event
	 *
	 * @return void
	 */
	protected function whenCookiePurposeChanged(CookiePurposeChanged $event): void
	{
		$translation = $this->filterTranslation($event->locale());

		if ($translation instanceof CookieTranslation) {
			$translation->setPurpose($event->purpose());

			return;
		}

		$this->translations->add(CookieTranslation::create($this, $event->locale(), $event->purpose()));
	}

	/**
	 * @param \App\Domain\Shared\ValueObject\Locale $locale
	 *
	 * @return \App\Domain\Cookie\CookieTranslation|NULL
	 */
	private function filterTranslation(Locale $locale): ?CookieTranslation
	{
		$translation = $this->translations->filter(static fn (CookieTranslation $translation): bool => $translation->locale()->equals($locale))->first();

		return $translation ?: NULL;
	}
}
