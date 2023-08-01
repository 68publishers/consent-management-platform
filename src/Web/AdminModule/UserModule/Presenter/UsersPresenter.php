<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Presenter;

use App\Application\Acl\UserResource;
use App\Web\AdminModule\Presenter\AdminPresenter;
use App\Web\AdminModule\UserModule\Control\UserList\UserListControl;
use App\Web\AdminModule\UserModule\Control\UserList\UserListControlFactoryInterface;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: UserResource::class, privilege: UserResource::READ)]
final class UsersPresenter extends AdminPresenter
{
    public function __construct(
        private readonly UserListControlFactoryInterface $userListControlFactory,
    ) {
        parent::__construct();
    }

    protected function startup(): void
    {
        parent::startup();

        $this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
    }

    protected function createComponentList(): UserListControl
    {
        return $this->userListControlFactory->create();
    }
}
