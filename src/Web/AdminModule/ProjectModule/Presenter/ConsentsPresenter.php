<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\Application\Acl\ProjectConsentResource;
use App\Web\AdminModule\ProjectModule\Control\ConsentList\ConsentListControl;
use App\Web\AdminModule\ProjectModule\Control\ConsentList\ConsentListControlFactoryInterface;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: ProjectConsentResource::class, privilege: ProjectConsentResource::READ)]
final class ConsentsPresenter extends SelectedProjectPresenter
{
    public function __construct(
        private readonly ConsentListControlFactoryInterface $consentListControlFactory,
    ) {
        parent::__construct();
    }

    protected function createComponentList(): ConsentListControl
    {
        return $this->consentListControlFactory->create($this->projectView->id);
    }
}
