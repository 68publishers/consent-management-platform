<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\Application\Acl\ProjectConsentSettingsResource;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use App\Web\AdminModule\ProjectModule\Control\ConsentSettingsList\ConsentSettingsListControl;
use App\Web\AdminModule\ProjectModule\Control\ConsentSettingsList\ConsentSettingsListControlFactoryInterface;

#[Allowed(resource: ProjectConsentSettingsResource::class, privilege: ProjectConsentSettingsResource::READ)]
final class ConsentSettingsPresenter extends SelectedProjectPresenter
{
	private ConsentSettingsListControlFactoryInterface $consentSettingsListControlFactory;

	/**
	 * @param \App\Web\AdminModule\ProjectModule\Control\ConsentSettingsList\ConsentSettingsListControlFactoryInterface $consentSettingsListControlFactory
	 */
	public function __construct(ConsentSettingsListControlFactoryInterface $consentSettingsListControlFactory)
	{
		parent::__construct();

		$this->consentSettingsListControlFactory = $consentSettingsListControlFactory;
	}

	/**
	 * @return \App\Web\AdminModule\ProjectModule\Control\ConsentSettingsList\ConsentSettingsListControl
	 */
	protected function createComponentList(): ConsentSettingsListControl
	{
		return $this->consentSettingsListControlFactory->create($this->projectView->id);
	}
}
