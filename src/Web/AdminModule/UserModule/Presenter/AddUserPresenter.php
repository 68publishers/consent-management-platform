<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Presenter;

use App\Application\Acl\UserResource;
use App\Web\AdminModule\Presenter\AdminPresenter;
use App\Web\AdminModule\UserModule\Control\UserForm\Event\UserCreatedEvent;
use App\Web\AdminModule\UserModule\Control\UserForm\Event\UserFormProcessingFailedEvent;
use App\Web\AdminModule\UserModule\Control\UserForm\UserFormControl;
use App\Web\AdminModule\UserModule\Control\UserForm\UserFormControlFactoryInterface;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: UserResource::class, privilege: UserResource::CREATE)]
final class AddUserPresenter extends AdminPresenter
{
    public function __construct(
        private readonly UserFormControlFactoryInterface $userFormControlFactory,
    ) {
        parent::__construct();
    }

    public function actionDefault(): void
    {
        $this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
    }

    protected function createComponentUserForm(): UserFormControl
    {
        $control = $this->userFormControlFactory->create();

        $control->addEventListener(UserCreatedEvent::class, function (UserCreatedEvent $event) {
            $this->subscribeFlashMessage(FlashMessage::success('user_created'));
            $this->redirect('EditUser:', ['id' => $event->userId()->toString()]);
        });

        $control->addEventListener(UserFormProcessingFailedEvent::class, function () {
            $this->subscribeFlashMessage(FlashMessage::error('user_creation_failed'));
        });

        return $control;
    }
}
