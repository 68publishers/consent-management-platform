<?php

declare(strict_types=1);

namespace App\Web\Ui\Menu;

use Contributte\MenuControl\IMenuItem;
use Nette\Application\PresenterFactory;
use Nette\Application\IPresenterFactory;
use Contributte\MenuControl\Security\IAuthorizator;
use App\Application\GlobalSettings\GlobalSettingsInterface;
use App\Web\AdminModule\CrawlerModule\Presenter\ScenariosPresenter;
use App\Web\AdminModule\CookieModule\Presenter\FoundCookiesPresenter;
use App\Web\AdminModule\CrawlerModule\Presenter\ScenarioSchedulersPresenter;
use App\Web\AdminModule\CookieModule\Presenter\FoundCookiesProjectsPresenter;
use SixtyEightPublishers\SmartNetteComponent\Exception\ForbiddenRequestException;
use SixtyEightPublishers\SmartNetteComponent\Authorization\ComponentAuthorizatorInterface;

final class MenuItemAuthorizator implements IAuthorizator
{
	private const CRAWLER_BASED_PRESENTERS = [
		ScenariosPresenter::class,
		ScenarioSchedulersPresenter::class,
		FoundCookiesPresenter::class,
		FoundCookiesProjectsPresenter::class,
	];

	public function __construct(
		private readonly ComponentAuthorizatorInterface $componentAuthorizator,
		private readonly IPresenterFactory $presenterFactory,
		private readonly GlobalSettingsInterface $globalSettings,
	) {
	}

	public function isMenuItemAllowed(IMenuItem $item): bool
	{
		if (empty($item->getActionTarget())) {
			return $this->checkChildren($item);
		}

		$target = rtrim($item->getActionTarget());

		if (str_ends_with($target, ':')) {
			$presenter = trim($target, ':');
			$action = 'default';
		} elseif (str_contains($target, ':')) {
			$explode = explode(':', $target);
			$action = array_pop($explode);
			$presenter = trim(implode(':', $explode), ':');
		} else {
			return TRUE;
		}

		assert($this->presenterFactory instanceof PresenterFactory);
		$presenter = $this->presenterFactory->formatPresenterClass($presenter);

		if (!$this->globalSettings->crawlerSettings()->enabled() && in_array($presenter, self::CRAWLER_BASED_PRESENTERS, TRUE)) {
			return FALSE;
		}

		try {
			$this->componentAuthorizator->checkPresenter($presenter);
			$this->componentAuthorizator->checkAction($presenter, $action);

			return TRUE;
		} catch (ForbiddenRequestException $e) {
			return FALSE;
		}
	}

	protected function checkChildren(IMenuItem $item): bool
	{
		foreach ($item->getItems() as $child) {
			if (TRUE === $child->isAllowed()) {
				return TRUE;
			}
		}

		return FALSE;
	}
}
