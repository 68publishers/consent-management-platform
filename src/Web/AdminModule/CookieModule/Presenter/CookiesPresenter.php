<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\Web\AdminModule\Presenter\AdminPresenter;

final class CookiesPresenter extends AdminPresenter
{
	/**
	 * @return void
	 */
	protected function startup(): void
	{
		parent::startup();

		$this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
	}
}
