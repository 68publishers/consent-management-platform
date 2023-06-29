<?php

declare(strict_types=1);

namespace App\Web\Ui\Menu;

use Contributte\MenuControl\IMenuItem;
use Nette\Application\IPresenterFactory;
use Contributte\MenuControl\Security\IAuthorizator;
use App\Application\GlobalSettings\GlobalSettingsInterface;
use App\Web\AdminModule\CrawlerModule\Presenter\ScenariosPresenter;
use App\Web\AdminModule\CookieModule\Presenter\FoundCookiesPresenter;
use App\Web\AdminModule\CrawlerModule\Presenter\ScenarioSchedulersPresenter;
use SixtyEightPublishers\SmartNetteComponent\Link\LinkAuthorizatorInterface;
use App\Web\AdminModule\CookieModule\Presenter\FoundCookiesProjectsPresenter;

final class MenuAuthorizator implements IAuthorizator
{
	private const CRAWLER_BASED_PRESENTERS = [
		ScenariosPresenter::class,
		ScenarioSchedulersPresenter::class,
		FoundCookiesPresenter::class,
		FoundCookiesProjectsPresenter::class,
	];

	private LinkAuthorizatorInterface $linkAuthorizator;

	private IPresenterFactory $presenterFactory;

	private GlobalSettingsInterface $globalSettings;

	public function __construct(
		LinkAuthorizatorInterface $linkAuthorizator,
		IPresenterFactory $presenterFactory,
		GlobalSettingsInterface $globalSettings
	) {
		$this->linkAuthorizator = $linkAuthorizator;
		$this->presenterFactory = $presenterFactory;
		$this->globalSettings = $globalSettings;
	}

	public function isMenuItemAllowed(IMenuItem $item): bool
	{
		if (empty($item->getAction())) {
			return $this->checkChildren($item);
		}

		$target = rtrim($item->getAction());

		if (':' === mb_substr($target, -1)) {
			$presenter = trim($target, ':');
			$action = 'default';
		} elseif (FALSE !== mb_strpos($target, ':')) {
			$explode = explode(':', $target);
			$action = array_pop($explode);
			$presenter = trim(implode(':', $explode), ':');
		}

		if (!isset($presenter, $action)) {
			return TRUE;
		}

		/** @noinspection PhpInternalEntityUsedInspection */
		$presenter = $this->presenterFactory->formatPresenterClass($presenter);

		if (!$this->globalSettings->crawlerSettings()->enabled() && in_array($presenter, self::CRAWLER_BASED_PRESENTERS, TRUE)) {
			return FALSE;
		}

		return $this->linkAuthorizator->isActionAllowed($presenter, $action);
	}

	protected function checkChildren(IMenuItem $item): bool
	{
		foreach ($item->getItems() as $child) {
			assert($child instanceof IMenuItem);

			if ($child->isAllowed()) {
				return TRUE;
			}
		}

		return FALSE;
	}
}
