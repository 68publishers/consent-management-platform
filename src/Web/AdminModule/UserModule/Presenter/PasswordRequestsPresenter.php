<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Presenter;

use App\Application\Acl\PasswordRequestResource;
use App\Web\AdminModule\Presenter\AdminPresenter;
use App\Web\AdminModule\UserModule\Control\PasswordRequestList\PasswordRequestListControl;
use App\Web\AdminModule\UserModule\Control\PasswordRequestList\PasswordRequestListControlFactoryInterface;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: PasswordRequestResource::class, privilege: PasswordRequestResource::READ)]
final class PasswordRequestsPresenter extends AdminPresenter
{
    public function __construct(
        private readonly PasswordRequestListControlFactoryInterface $passwordRequestListControlFactory,
    ) {
        parent::__construct();
    }

    protected function startup(): void
    {
        parent::startup();

        $this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
    }

    protected function createComponentList(): PasswordRequestListControl
    {
        return $this->passwordRequestListControlFactory->create();
    }
}
