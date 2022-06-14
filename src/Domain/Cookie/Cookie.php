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

	private Collection $translations;

	/**
	 * @param \App\Domain\Cookie\Command\CreateCookieCommand        $command
	 * @param \App\Domain\Cookie\CheckCategoryExistsInterface       $checkCategoryExists
	 * @param \App\Domain\Cookie\CheckCookieProviderExistsInterface $checkCookieProviderExists
	 *
	 * @return static
	 */
	public static function create(CreateCookieCommand $command, CheckCategoryExistsInterface $checkCategoryExists, CheckCookieProviderExistsInterface $checkCookieProviderExists): self
	{
		$cookie = new self();

		$id = NULL !== $command->cookieId() ? CookieId::fromString($command->cookieId()) : CookieId::new();
		$categoryId = CategoryId::fromString($command->categoryId());
		$cookieProviderId = CookieProviderId::fromString($command->cookieProviderId());
		$name = Name::fromValue($command->name());
		$purposes = array_map(static fn (string $purpose): Purpose => Purpose::fromValue($purpose), $command->purposes());
		$processingTimes = array_map(static fn (string $processingTime): ProcessingTime => ProcessingTime::fromValue($processingTime), $command->processingTimes());

		$checkCategoryExists($categoryId);
		$checkCookieProviderExists($cookieProviderId);

		$cookie->recordThat(CookieCreated::create($id, $categoryId, $cookieProviderId, $name, $purposes, $processingTimes));

		return $cookie;
	}

	/**
	 * @param \App\Domain\Cookie\Command\UpdateCookieCommand  $command
	 * @param \App\Domain\Cookie\CheckCategoryExistsInterface $checkCategoryExists
	 *
	 * @return void
	 */
	public function update(UpdateCookieCommand $command, CheckCategoryExistsInterface $checkCategoryExists): void
	{
		if (NULL !== $command->categoryId()) {
			$this->changeCategoryId(CategoryId::fromString($command->categoryId()), $checkCategoryExists);
		}

		if (NULL !== $command->name()) {
			$this->changeName(Name::fromValue($command->name()));
		}

		if (NULL !== $command->purposes()) {
			foreach ($command->purposes() as $locale => $purpose) {
				$this->changePurpose(Locale::fromValue($locale), Purpose::fromValue($purpose));
			}
		}

		if (NULL !== $command->processingTimes()) {
			foreach ($command->processingTimes() as $locale => $processingTime) {
				$this->changeProcessingTime(Locale::fromValue($locale), ProcessingTime::fromValue($processingTime));
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
	 * @param \App\Domain\Cookie\ValueObject\Name $name
	 *
	 * @return void
	 */
	public function changeName(Name $name): void
	{
		if (!$this->name->equals($name)) {
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
	 * @param \App\Domain\Shared\ValueObject\Locale         $locale
	 * @param \App\Domain\Cookie\ValueObject\ProcessingTime $processingTime
	 *
	 * @return void
	 */
	public function changeProcessingTime(Locale $locale, ProcessingTime $processingTime): void
	{
		$translation = $this->filterTranslation($locale);

		if (!$translation instanceof CookieTranslation || !$translation->processingTime()->equals($processingTime)) {
			$this->recordThat(CookieProcessingTimeChanged::create($this->id, $locale, $processingTime));
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
		$this->translations = new ArrayCollection();
		$locales = array_unique(array_merge(array_keys($event->purposes()), array_keys($event->processingTimes())));

		foreach ($locales as $locale) {
			$this->translations->add(CookieTranslation::create(
				$this,
				Locale::fromValue($locale),
				$event->purposes()[$locale] ?? Purpose::fromValue(''),
				$event->processingTimes()[$locale] ?? ProcessingTime::fromValue('')
			));
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

		$this->translations->add(CookieTranslation::create($this, $event->locale(), $event->purpose(), ProcessingTime::fromValue('')));
	}

	/**
	 * @param \App\Domain\Cookie\Event\CookieProcessingTimeChanged $event
	 *
	 * @return void
	 */
	protected function whenCookieProcessingTimeChanged(CookieProcessingTimeChanged $event): void
	{
		$translation = $this->filterTranslation($event->locale());

		if ($translation instanceof CookieTranslation) {
			$translation->setProcessingTime($event->processingTime());

			return;
		}

		$this->translations->add(CookieTranslation::create($this, $event->locale(), Purpose::fromValue(''), $event->processingTime()));
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
