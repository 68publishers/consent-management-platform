<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings;

use DateTimeImmutable;
use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Shared\ValueObject\Locales;
use App\Domain\Shared\ValueObject\LocalesConfig;
use App\Domain\GlobalSettings\Event\GlobalSettingsCreated;
use App\Domain\GlobalSettings\Event\GlobalSettingsUpdated;
use App\Domain\GlobalSettings\ValueObject\GlobalSettingsId;
use App\Domain\GlobalSettings\Command\StoreGlobalSettingsCommand;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootTrait;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootInterface;

final class GlobalSettings implements AggregateRootInterface
{
	use AggregateRootTrait;

	private GlobalSettingsId $id;

	private DateTimeImmutable $createdAt;

	private DateTimeImmutable $lastUpdateAt;

	private LocalesConfig $locales;

	/**
	 * @param \App\Domain\GlobalSettings\Command\StoreGlobalSettingsCommand $command
	 *
	 * @return static
	 */
	public static function create(StoreGlobalSettingsCommand $command): self
	{
		$globalSettings = new self();
		$locales = Locales::empty();
		$defaultLocale = Locale::fromValue($command->defaultLocale());

		foreach ($command->locales() as $locale) {
			$locales = $locales->with(Locale::fromValue($locale));
		}

		$globalSettings->recordThat(GlobalSettingsCreated::create(GlobalSettingsId::new(), LocalesConfig::create($locales, $defaultLocale)));

		return $globalSettings;
	}

	/**
	 * @param \App\Domain\GlobalSettings\Command\StoreGlobalSettingsCommand $command
	 *
	 * @return void
	 */
	public function update(StoreGlobalSettingsCommand $command): void
	{
		$locales = Locales::empty();
		$defaultLocale = Locale::fromValue($command->defaultLocale());

		foreach ($command->locales() as $locale) {
			$locales = $locales->with(Locale::fromValue($locale));
		}

		$locales = LocalesConfig::create($locales, $defaultLocale);

		if (!$this->locales->equals($locales)) {
			$this->recordThat(GlobalSettingsUpdated::create($this->id, $locales));
		}
	}

	/**
	 * @return \SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId
	 */
	public function aggregateId(): AggregateId
	{
		return AggregateId::fromUuid($this->id->id());
	}

	/**
	 * @param \App\Domain\GlobalSettings\Event\GlobalSettingsCreated $event
	 *
	 * @return void
	 */
	protected function whenGlobalSettingsCreated(GlobalSettingsCreated $event): void
	{
		$this->id = $event->globalSettingsId();
		$this->createdAt = $event->createdAt();
		$this->lastUpdateAt = $event->createdAt();
		$this->locales = $event->locales();
	}

	/**
	 * @param \App\Domain\GlobalSettings\Event\GlobalSettingsUpdated $event
	 *
	 * @return void
	 */
	protected function whenGlobalSettingsUpdated(GlobalSettingsUpdated $event): void
	{
		$this->lastUpdateAt = $event->createdAt();
		$this->locales = $event->locales();
	}
}
