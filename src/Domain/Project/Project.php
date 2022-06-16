<?php

declare(strict_types=1);

namespace App\Domain\Project;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Project\ValueObject\Code;
use App\Domain\Project\ValueObject\Name;
use App\Domain\Project\ValueObject\Color;
use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Shared\ValueObject\Locales;
use Doctrine\Common\Collections\Collection;
use App\Domain\Project\Event\ProjectCreated;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Project\ValueObject\Description;
use App\Domain\Project\Event\ProjectCodeChanged;
use App\Domain\Project\Event\ProjectNameChanged;
use App\Domain\Shared\ValueObject\LocalesConfig;
use Doctrine\Common\Collections\ArrayCollection;
use App\Domain\Project\Event\ProjectColorChanged;
use App\Domain\Project\Event\ProjectLocalesChanged;
use App\Domain\Project\Command\CreateProjectCommand;
use App\Domain\Project\Command\UpdateProjectCommand;
use App\Domain\Project\Event\ProjectTimezoneChanged;
use App\Domain\Project\Event\ProjectActiveStateChanged;
use App\Domain\Project\Event\ProjectDescriptionChanged;
use App\Domain\Project\Event\ProjectCookieProviderAdded;
use App\Domain\Project\Event\ProjectCookieProviderRemoved;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootTrait;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootInterface;

final class Project implements AggregateRootInterface
{
	use AggregateRootTrait;

	private ProjectId $id;

	protected DateTimeImmutable $createdAt;

	private Name $name;

	private Code $code;

	private Color $color;

	private Description $description;

	private bool $active;

	private LocalesConfig $locales;

	private DateTimeZone $timezone;

	/** @var \Doctrine\Common\Collections\Collection|\App\Domain\User\ProjectHasCookieProvider[] */
	private Collection $cookieProviders;

	/**
	 * @param \App\Domain\Project\Command\CreateProjectCommand $command
	 * @param \App\Domain\Project\CheckCodeUniquenessInterface $checkCodeUniqueness
	 *
	 * @return static
	 */
	public static function create(CreateProjectCommand $command, CheckCodeUniquenessInterface $checkCodeUniqueness): self
	{
		$project = new self();

		$projectId = NULL !== $command->projectId() ? ProjectId::fromString($command->projectId()) : ProjectId::new();
		$name = Name::fromValue($command->name());
		$code = Code::fromValidCode($command->code());
		$description = Description::fromValue($command->description());
		$color = Color::fromValidColor($command->color());
		$locales = Locales::empty();
		$defaultLocale = Locale::fromValue($command->defaultLocale());
		$timezone = new DateTimeZone($command->timezone());

		foreach ($command->locales() as $locale) {
			$locales = $locales->with(Locale::fromValue($locale));
		}

		$checkCodeUniqueness($projectId, $code);

		$project->recordThat(ProjectCreated::create($projectId, $name, $code, $description, $color, $command->active(), LocalesConfig::create($locales, $defaultLocale), $timezone));
		$project->setCookieProviders(array_map(static fn (string $cookieProviderId): CookieProviderId => CookieProviderId::fromString($cookieProviderId), $command->cookieProviderIds()));

		return $project;
	}

	/**
	 * @param \App\Domain\Project\Command\UpdateProjectCommand $command
	 * @param \App\Domain\Project\CheckCodeUniquenessInterface $checkCodeUniqueness
	 *
	 * @return void
	 */
	public function update(UpdateProjectCommand $command, CheckCodeUniquenessInterface $checkCodeUniqueness): void
	{
		if (NULL !== $command->name()) {
			$this->changeName(Name::fromValue($command->name()));
		}

		if (NULL !== $command->code()) {
			$this->changeCode(Code::fromValidCode($command->code()), $checkCodeUniqueness);
		}

		if (NULL !== $command->color()) {
			$this->changeColor(Color::fromValidColor($command->color()));
		}

		if (NULL !== $command->description()) {
			$this->changeDescription(Description::fromValue($command->description()));
		}

		if (NULL !== $command->active()) {
			$this->changeActiveState($command->active());
		}

		if (NULL !== $command->locales() && NULL !== $command->defaultLocale()) {
			$locales = Locales::empty();
			$defaultLocale = Locale::fromValue($command->defaultLocale());

			foreach ($command->locales() as $locale) {
				$locales = $locales->with(Locale::fromValue($locale));
			}

			$this->changeLocales(LocalesConfig::create($locales, $defaultLocale));
		}

		if (NULL !== $command->timezone()) {
			$this->changeTimezone(new DateTimeZone($command->timezone()));
		}

		if (NULL !== $command->cookieProviderIds()) {
			$this->setCookieProviders(array_map(static fn (string $cookieProviderId): CookieProviderId => CookieProviderId::fromString($cookieProviderId), $command->cookieProviderIds()));
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
	 * @param \App\Domain\Project\ValueObject\Name $name
	 *
	 * @return void
	 */
	public function changeName(Name $name): void
	{
		if (!$this->name->equals($name)) {
			$this->recordThat(ProjectNameChanged::create($this->id, $name));
		}
	}

	/**
	 * @param \App\Domain\Project\ValueObject\Code             $code
	 * @param \App\Domain\Project\CheckCodeUniquenessInterface $checkCodeUniqueness
	 *
	 * @return void
	 */
	public function changeCode(Code $code, CheckCodeUniquenessInterface $checkCodeUniqueness): void
	{
		$code = Code::fromValidCode($code->value());

		if (!$this->code->equals($code)) {
			$checkCodeUniqueness($this->id, $code);
			$this->recordThat(ProjectCodeChanged::create($this->id, $code));
		}
	}

	/**
	 * @param \App\Domain\Project\ValueObject\Color $color
	 *
	 * @return void
	 */
	public function changeColor(Color $color): void
	{
		$color = Color::fromValidColor($color->value());

		if (!$this->color->equals($color)) {
			$this->recordThat(ProjectColorChanged::create($this->id, $color));
		}
	}

	/**
	 * @param \App\Domain\Project\ValueObject\Description $description
	 *
	 * @return void
	 */
	public function changeDescription(Description $description): void
	{
		if (!$this->description->equals($description)) {
			$this->recordThat(ProjectDescriptionChanged::create($this->id, $description));
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
			$this->recordThat(ProjectActiveStateChanged::create($this->id, $active));
		}
	}

	/**
	 * @param \App\Domain\Shared\ValueObject\LocalesConfig $locales
	 *
	 * @return void
	 */
	public function changeLocales(LocalesConfig $locales): void
	{
		if (!$this->locales->equals($locales)) {
			$this->recordThat(ProjectLocalesChanged::create($this->id, $locales));
		}
	}

	/**
	 * @param \DateTimeZone $timezone
	 *
	 * @return void
	 */
	public function changeTimezone(DateTimeZone $timezone): void
	{
		if ($this->timezone->getName() !== $timezone->getName()) {
			$this->recordThat(ProjectTimezoneChanged::create($this->id, $timezone));
		}
	}

	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId[] $cookieProviderIds
	 *
	 * @return void
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

	/**
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 *
	 * @return void
	 */
	public function addCookieProvider(CookieProviderId $cookieProviderId): void
	{
		if (!$this->hasCookieProvider($this->cookieProviders, $cookieProviderId)) {
			$this->recordThat(ProjectCookieProviderAdded::create($this->id, $cookieProviderId));
		}
	}

	/**
	 * @param \App\Domain\Project\Event\ProjectCreated $event
	 *
	 * @return void
	 */
	protected function whenProjectCreated(ProjectCreated $event): void
	{
		$this->id = $event->projectId();
		$this->createdAt = $event->createdAt();
		$this->name = $event->name();
		$this->code = $event->code();
		$this->color = $event->color();
		$this->description = $event->description();
		$this->active = $event->active();
		$this->locales = $event->locales();
		$this->timezone = $event->timezone();
		$this->cookieProviders = new ArrayCollection();
	}

	/**
	 * @param \App\Domain\Project\Event\ProjectNameChanged $event
	 *
	 * @return void
	 */
	protected function whenProjectNameChanged(ProjectNameChanged $event): void
	{
		$this->name = $event->name();
	}

	/**
	 * @param \App\Domain\Project\Event\ProjectCodeChanged $event
	 *
	 * @return void
	 */
	protected function whenProjectCodeChanged(ProjectCodeChanged $event): void
	{
		$this->code = $event->code();
	}

	/**
	 * @param \App\Domain\Project\Event\ProjectColorChanged $event
	 *
	 * @return void
	 */
	protected function whenProjectColorChanged(ProjectColorChanged $event): void
	{
		$this->color = $event->color();
	}

	/**
	 * @param \App\Domain\Project\Event\ProjectDescriptionChanged $event
	 *
	 * @return void
	 */
	protected function whenProjectDescriptionChanged(ProjectDescriptionChanged $event): void
	{
		$this->description = $event->description();
	}

	/**
	 * @param \App\Domain\Project\Event\ProjectActiveStateChanged $event
	 *
	 * @return void
	 */
	protected function whenProjectActiveStateChanged(ProjectActiveStateChanged $event): void
	{
		$this->active = $event->active();
	}

	/**
	 * @param \App\Domain\Project\Event\ProjectLocalesChanged $event
	 *
	 * @return void
	 */
	protected function whenProjectLocalesChanged(ProjectLocalesChanged $event): void
	{
		$this->locales = $event->locales();
	}

	/**
	 * @param \App\Domain\Project\Event\ProjectTimezoneChanged $event
	 *
	 * @return void
	 */
	protected function whenProjectTimezoneChanged(ProjectTimezoneChanged $event): void
	{
		$this->timezone = $event->timezone();
	}

	/**
	 * @param \App\Domain\Project\Event\ProjectCookieProviderAdded $event
	 *
	 * @return void
	 */
	protected function whenProjectCookieProviderAdded(ProjectCookieProviderAdded $event): void
	{
		$this->cookieProviders->add(ProjectHasCookieProvider::create($this, $event->cookieProviderId()));
	}

	/**
	 * @param \App\Domain\Project\Event\ProjectCookieProviderRemoved $event
	 *
	 * @return void
	 */
	protected function whenProjectCookieProviderRemoved(ProjectCookieProviderRemoved $event): void
	{
		$projectHasCookieProvider = $this->hasCookieProvider($this->cookieProviders, $event->cookieProviderId());

		if ($projectHasCookieProvider instanceof ProjectHasCookieProvider) {
			$this->cookieProviders->removeElement($projectHasCookieProvider);
		}
	}

	/**
	 * @param \Doctrine\Common\Collections\Collection|\App\Domain\CookieProvider\ValueObject\CookieProviderId[] $collection
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId                                           $cookieProviderId
	 *
	 * @return object|FALSE
	 */
	private function hasCookieProvider(iterable $collection, CookieProviderId $cookieProviderId)
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

		return FALSE;
	}
}
