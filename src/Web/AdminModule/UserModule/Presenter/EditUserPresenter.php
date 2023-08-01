<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Presenter;

use App\Application\Acl\UserResource;
use App\ReadModel\User\UserView;
use App\Web\AdminModule\Presenter\AdminPresenter;
use App\Web\AdminModule\UserModule\Control\NotificationPreferences\Event\NotificationPreferencesProcessingFailedEvent;
use App\Web\AdminModule\UserModule\Control\NotificationPreferences\Event\NotificationPreferencesUpdatedEvent;
use App\Web\AdminModule\UserModule\Control\NotificationPreferences\NotificationPreferencesControl;
use App\Web\AdminModule\UserModule\Control\NotificationPreferences\NotificationPreferencesControlFactoryInterface;
use App\Web\AdminModule\UserModule\Control\UserForm\Event\UserFormProcessingFailedEvent;
use App\Web\AdminModule\UserModule\Control\UserForm\Event\UserUpdatedEvent;
use App\Web\AdminModule\UserModule\Control\UserForm\UserFormControl;
use App\Web\AdminModule\UserModule\Control\UserForm\UserFormControlFactoryInterface;
use App\Web\Ui\Form\FormFactoryInterface;
use Nette\Application\AbortException;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;
use SixtyEightPublishers\UserBundle\ReadModel\Query\GetUserByIdQuery;

#[Allowed(resource: UserResource::class, privilege: UserResource::UPDATE)]
final class EditUserPresenter extends AdminPresenter
{
    private UserFormControlFactoryInterface $userFormControlFactory;

    private NotificationPreferencesControlFactoryInterface $notificationPreferencesControlFactory;

    private QueryBusInterface $queryBus;

    private UserView $userView;

    public function __construct(UserFormControlFactoryInterface $userFormControlFactory, NotificationPreferencesControlFactoryInterface $notificationPreferencesControlFactory, QueryBusInterface $queryBus)
    {
        parent::__construct();

        $this->userFormControlFactory = $userFormControlFactory;
        $this->notificationPreferencesControlFactory = $notificationPreferencesControlFactory;
        $this->queryBus = $queryBus;
    }

    /**
     * @throws AbortException
     */
    public function actionDefault(string $id): void
    {
        $userView = UserId::isValid($id) ? $this->queryBus->dispatch(GetUserByIdQuery::create($id)) : null;

        if (!$userView instanceof UserView) {
            $this->subscribeFlashMessage(FlashMessage::warning('user_not_found'));
            $this->redirect('Users:');
        }

        $this->userView = $userView;

        $this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
        $this->addBreadcrumbItem($this->userView->username->value());
    }

    protected function createComponentUserForm(): UserFormControl
    {
        $control = $this->userFormControlFactory->create($this->userView);

        $control->setFormFactoryOptions([
            FormFactoryInterface::OPTION_AJAX => true,
        ]);

        $control->addEventListener(UserUpdatedEvent::class, function () {
            $this->subscribeFlashMessage(FlashMessage::success('user_updated'));
        });

        $control->addEventListener(UserFormProcessingFailedEvent::class, function () {
            $this->subscribeFlashMessage(FlashMessage::error('user_update_failed'));
        });

        return $control;
    }

    protected function createComponentNotificationPreferences(): NotificationPreferencesControl
    {
        $control = $this->notificationPreferencesControlFactory->create($this->userView);

        $control->setFormFactoryOptions([
            FormFactoryInterface::OPTION_AJAX => true,
        ]);

        $control->addEventListener(NotificationPreferencesUpdatedEvent::class, function () {
            $this->subscribeFlashMessage(FlashMessage::success('notification_preferences_updated'));
        });

        $control->addEventListener(NotificationPreferencesProcessingFailedEvent::class, function () {
            $this->subscribeFlashMessage(FlashMessage::error('notification_preferences_update_failed'));
        });

        return $control;
    }
}
