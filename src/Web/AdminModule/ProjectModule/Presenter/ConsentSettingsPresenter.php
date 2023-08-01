<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\Application\Acl\ProjectConsentSettingsResource;
use App\Web\AdminModule\ProjectModule\Control\ConsentSettingsList\ConsentSettingsListControl;
use App\Web\AdminModule\ProjectModule\Control\ConsentSettingsList\ConsentSettingsListControlFactoryInterface;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: ProjectConsentSettingsResource::class, privilege: ProjectConsentSettingsResource::READ)]
final class ConsentSettingsPresenter extends SelectedProjectPresenter
{
    public function __construct(
        private readonly ConsentSettingsListControlFactoryInterface $consentSettingsListControlFactory,
    ) {
        parent::__construct();
    }

    protected function createComponentList(): ConsentSettingsListControl
    {
        return $this->consentSettingsListControlFactory->create($this->projectView->id);
    }
}
