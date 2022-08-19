<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

use App\Application\Localization\Locales;
use App\ReadModel\GlobalSettings\GlobalSettingsView;
use App\ReadModel\GlobalSettings\GetGlobalSettingsQuery;
use App\Domain\Shared\ValueObject\Locale as LocaleValueObject;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;

final class GlobalSettingsFactory implements GlobalSettingsFactoryInterface
{
	private QueryBusInterface $queryBus;

	private Locales $locales;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface $queryBus
	 * @param \App\Application\Localization\Locales                          $locales
	 */
	public function __construct(QueryBusInterface $queryBus, Locales $locales)
	{
		$this->queryBus = $queryBus;
		$this->locales = $locales;
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(): GlobalSettingsInterface
	{
		$globalSettingsView = $this->queryBus->dispatch(GetGlobalSettingsQuery::create());

		if (!$globalSettingsView instanceof GlobalSettingsView) {
			return GlobalSettings::default();
		}

		$locales = [];
		$list = $this->locales->get();

		foreach ($globalSettingsView->locales->locales()->all() as $locale) {
			assert($locale instanceof LocaleValueObject);

			$localeValue = $locale->value();
			$locales[] = Locale::create($localeValue, $list[$localeValue] ?? $localeValue);
		}

		$defaultLocaleValue = $globalSettingsView->locales->defaultLocale()->value();

		return new GlobalSettings(
			$locales,
			Locale::create($defaultLocaleValue, $list[$defaultLocaleValue] ?? $defaultLocaleValue),
			$globalSettingsView->apiCache
		);
	}
}
