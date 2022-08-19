<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings;

use DateTimeImmutable;
use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Shared\ValueObject\Locales;
use App\Domain\Shared\ValueObject\LocalesConfig;
use App\Domain\GlobalSettings\ValueObject\ApiCache;
use App\Domain\GlobalSettings\Event\GlobalSettingsCreated;
use App\Domain\GlobalSettings\ValueObject\GlobalSettingsId;
use App\Domain\GlobalSettings\Event\ApiCacheSettingsChanged;
use App\Domain\GlobalSettings\Event\LocalizationSettingsChanged;
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

	private ApiCache $apiCache;

	/**
	 * @return static
	 */
	public static function createEmpty(): self
	{
		$globalSettings = new self();

		$globalSettings->recordThat(GlobalSettingsCreated::create(GlobalSettingsId::new()));

		return $globalSettings;
	}

	/**
	 * @param \App\Domain\Shared\ValueObject\LocalesConfig $localesConfig
	 *
	 * @return void
	 */
	public function updateLocalizationSettings(LocalesConfig $localesConfig): void
	{
		if (!$this->locales->equals($localesConfig)) {
			$this->recordThat(LocalizationSettingsChanged::create($this->id, $localesConfig));
		}
	}

	/**
	 * @param \App\Domain\GlobalSettings\ValueObject\ApiCache $apiCache
	 *
	 * @return void
	 */
	public function updateApiCacheSettings(ApiCache $apiCache): void
	{
		if (!$this->apiCache->equals($apiCache)) {
			$this->recordThat(ApiCacheSettingsChanged::create($this->id, $apiCache));
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
		$this->locales = LocalesConfig::create(Locales::reconstitute(['en']), Locale::fromValue('en')); // setup defaults to en
		$this->apiCache = ApiCache::create([]);
	}

	/**
	 * @param \App\Domain\GlobalSettings\Event\LocalizationSettingsChanged $event
	 *
	 * @return void
	 */
	protected function whenLocalizationSettingsChanged(LocalizationSettingsChanged $event): void
	{
		$this->lastUpdateAt = $event->createdAt();
		$this->locales = $event->locales();
	}

	/**
	 * @param \App\Domain\GlobalSettings\Event\ApiCacheSettingsChanged $event
	 *
	 * @return void
	 */
	protected function whenApiCacheSettingsChanged(ApiCacheSettingsChanged $event): void
	{
		$this->lastUpdateAt = $event->createdAt();
		$this->apiCache = $event->apiCache();
	}
}
