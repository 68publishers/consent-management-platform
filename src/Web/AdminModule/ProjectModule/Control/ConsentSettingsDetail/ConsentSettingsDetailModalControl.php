<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail;

use App\Web\Ui\Modal\AbstractModalControl;
use App\ReadModel\ConsentSettings\ConsentSettingsView;

final class ConsentSettingsDetailModalControl extends AbstractModalControl
{
	private ConsentSettingsView $consentSettingsView;

	private ConsentSettingsDetailControlFactoryInterface $consentHistoryControlFactory;

	/**
	 * @param \App\ReadModel\ConsentSettings\ConsentSettingsView                                                            $consentSettingsView
	 * @param \App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail\ConsentSettingsDetailControlFactoryInterface $consentHistoryControlFactory
	 */
	public function __construct(ConsentSettingsView $consentSettingsView, ConsentSettingsDetailControlFactoryInterface $consentHistoryControlFactory)
	{
		$this->consentSettingsView = $consentSettingsView;
		$this->consentHistoryControlFactory = $consentHistoryControlFactory;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->consentSettingsView = $this->consentSettingsView;
	}

	/**
	 * @return \App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail\ConsentSettingsDetailControl
	 */
	protected function createComponentConsentSettingsDetail(): ConsentSettingsDetailControl
	{
		return $this->consentHistoryControlFactory->create($this->consentSettingsView);
	}
}
