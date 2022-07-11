<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail;

use App\Web\Ui\Control;
use App\ReadModel\ConsentSettings\ConsentSettingsView;

final class ConsentSettingsDetailControl extends Control
{
	private ConsentSettingsView $consentSettingsView;

	/**
	 * @param \App\ReadModel\ConsentSettings\ConsentSettingsView $consentSettingsView
	 */
	public function __construct(ConsentSettingsView $consentSettingsView)
	{
		$this->consentSettingsView = $consentSettingsView;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->consentSettingsView = $this->consentSettingsView;
	}
}
