<?php

declare(strict_types=1);

namespace App\Web\Ui\Menu;

use Contributte\MenuControl\IMenuItem;
use Nette\Application\IPresenterFactory;
use Contributte\MenuControl\Security\IAuthorizator;
use SixtyEightPublishers\SmartNetteComponent\Link\LinkAuthorizatorInterface;

final class MenuAuthorizator implements IAuthorizator
{
	private LinkAuthorizatorInterface $linkAuthorizator;

	private IPresenterFactory $presenterFactory;

	/**
	 * @param \SixtyEightPublishers\SmartNetteComponent\Link\LinkAuthorizatorInterface $linkAuthorizator
	 * @param \Nette\Application\IPresenterFactory                                     $presenterFactory
	 */
	public function __construct(LinkAuthorizatorInterface $linkAuthorizator, IPresenterFactory $presenterFactory)
	{
		$this->linkAuthorizator = $linkAuthorizator;
		$this->presenterFactory = $presenterFactory;
	}

	/**
	 * @param \Contributte\MenuControl\IMenuItem $item
	 *
	 * @return bool
	 */
	public function isMenuItemAllowed(IMenuItem $item): bool
	{
		if (empty($item->getAction())) {
			return $this->checkChildren($item);
		}

		$target = rtrim($item->getAction());

		if (':' === substr($target, -1)) {
			$presenter = trim($target, ':');
			$action = 'default';
		} elseif (FALSE !== strpos($target, ':')) {
			$explode = explode(':', $target);
			$action = array_pop($explode);
			$presenter = trim(implode(':', $explode), ':');
		}

		if (!isset($presenter, $action)) {
			return TRUE;
		}

		/** @noinspection PhpInternalEntityUsedInspection */
		$presenter = $this->presenterFactory->formatPresenterClass($presenter);

		return $this->linkAuthorizator->isActionAllowed($presenter, $action);
	}

	/**
	 * @param \Contributte\MenuControl\IMenuItem $item
	 *
	 * @return bool
	 */
	protected function checkChildren(IMenuItem $item): bool
	{
		/** @var \Contributte\MenuControl\IMenuItem $child */
		foreach ($item->getItems() as $child) {
			if ($child->isAllowed()) {
				return TRUE;
			}
		}

		return FALSE;
	}
}
