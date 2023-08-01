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
    private ConsentListControlFactoryInterface $consentListControlFactory;

    public function __construct(ConsentListControlFactoryInterface $consentListControlFactory)
    {
        parent::__construct();

        $this->consentListControlFactory = $consentListControlFactory;
    }

    protected function createComponentList(): ConsentListControl
    {
        return $this->consentListControlFactory->create($this->projectView->id);
    }
}
