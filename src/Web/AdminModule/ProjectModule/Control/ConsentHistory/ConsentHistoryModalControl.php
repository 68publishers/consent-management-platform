<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentHistory;

use App\ReadModel\Consent\ConsentView;
use App\Web\Ui\Modal\AbstractModalControl;

final class ConsentHistoryModalControl extends AbstractModalControl
{
	private ConsentView $consentView;

	private ConsentHistoryControlFactoryInterface $consentHistoryControlFactory;

	/**
	 * @param \App\ReadModel\Consent\ConsentView                                                              $consentView
	 * @param \App\Web\AdminModule\ProjectModule\Control\ConsentHistory\ConsentHistoryControlFactoryInterface $consentHistoryControlFactory
	 */
	public function __construct(ConsentView $consentView, ConsentHistoryControlFactoryInterface $consentHistoryControlFactory)
	{
		$this->consentView = $consentView;
		$this->consentHistoryControlFactory = $consentHistoryControlFactory;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->consentView = $this->consentView;
	}

	/**
	 * @return \App\Web\AdminModule\ProjectModule\Control\ConsentHistory\ConsentHistoryControl
	 */
	protected function createComponentHistory(): ConsentHistoryControl
	{
		return $this->consentHistoryControlFactory->create($this->consentView->id, $this->consentView->projectId);
	}
}
