<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\Web\AdminModule\ProjectModule\Control\ConsentList\ConsentListControl;
use App\Web\AdminModule\ProjectModule\Control\ConsentList\ConsentListControlFactoryInterface;

final class ConsentsPresenter extends SelectedProjectPresenter
{
	private ConsentListControlFactoryInterface $consentListControlFactory;

	/**
	 * @param \App\Web\AdminModule\ProjectModule\Control\ConsentList\ConsentListControlFactoryInterface $consentListControlFactory
	 */
	public function __construct(ConsentListControlFactoryInterface $consentListControlFactory)
	{
		parent::__construct();

		$this->consentListControlFactory = $consentListControlFactory;
	}

	/**
	 * @return \App\Web\AdminModule\ProjectModule\Control\ConsentList\ConsentListControl
	 */
	protected function createComponentList(): ConsentListControl
	{
		return $this->consentListControlFactory->create($this->projectView->id);
	}
}
