<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

use SixtyEightPublishers\i18n\Lists\LanguageList;
use App\ReadModel\GlobalSettings\GlobalSettingsView;
use App\ReadModel\GlobalSettings\GetGlobalSettingsQuery;
use App\Domain\Shared\ValueObject\Locale as LocaleValueObject;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;

final class LazyGlobalSettings implements GlobalSettingsInterface
{
	private QueryBusInterface $queryBus;

	private LanguageList $languageList;

	private ?GlobalSettings $inner = NULL;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface $queryBus
	 * @param \SixtyEightPublishers\i18n\Lists\LanguageList                  $languageList
	 */
	public function __construct(QueryBusInterface $queryBus, LanguageList $languageList)
	{
		$this->queryBus = $queryBus;
		$this->languageList = $languageList;
	}

	/**
	 * {@inheritDoc}
	 */
	public function locales(): array
	{
		return $this->getInner()->locales();
	}

	/**
	 * {@inheritDoc}
	 */
	public function defaultLocale(): Locale
	{
		return $this->getInner()->defaultLocale();
	}

	/**
	 * {@inheritDoc}
	 */
	public function refresh(): void
	{
		$this->inner = NULL;
	}

	/**
	 * @return \App\Application\GlobalSettings\GlobalSettings
	 */
	private function getInner(): GlobalSettings
	{
		if (NULL !== $this->inner) {
			return $this->inner;
		}

		$globalSettingsView = $this->queryBus->dispatch(GetGlobalSettingsQuery::create());

		if (!$globalSettingsView instanceof GlobalSettingsView) {
			return $this->inner = GlobalSettings::default();
		}

		$locales = [];
		$list = $this->languageList->getList();

		foreach ($globalSettingsView->locales->locales()->all() as $locale) {
			assert($locale instanceof LocaleValueObject);

			$localeValue = $locale->value();
			$locales[] = Locale::create($localeValue, $list[$localeValue] ?? $localeValue);
		}

		$defaultLocaleValue = $globalSettingsView->locales->defaultLocale()->value();

		return $this->inner = new GlobalSettings(
			$locales,
			Locale::create($defaultLocaleValue, $list[$defaultLocaleValue] ?? $defaultLocaleValue)
		);
	}
}
