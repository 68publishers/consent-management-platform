<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\NotificationPreferences;

use App\Domain\User\Command\ChangeNotificationPreferencesCommand;
use App\Domain\User\ValueObject\NotificationType;
use App\ReadModel\User\UserView;
use App\Web\AdminModule\UserModule\Control\NotificationPreferences\Event\NotificationPreferencesProcessingFailedEvent;
use App\Web\AdminModule\UserModule\Control\NotificationPreferences\Event\NotificationPreferencesUpdatedEvent;
use App\Web\Ui\Control;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use Nette\Application\UI\Form;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use Throwable;

final class NotificationPreferencesControl extends Control
{
    use FormFactoryOptionsTrait;

    public function __construct(
        private readonly UserView $userVIew,
        private readonly FormFactoryInterface $formFactory,
        private readonly CommandBusInterface $commandBus,
    ) {}

    protected function createComponentForm(): Form
    {
        $form = $this->formFactory->create($this->getFormFactoryOptions());
        $translator = $this->getPrefixedTranslator();

        $form->setTranslator($translator);

        $form->addCheckboxList('notifications', 'notifications.field')
            ->setItems(NotificationType::values(), false)
            ->checkDefaultValue(false)
            ->setDefaultValue($this->userVIew->notificationPreferences->toArray());

        $form->addProtection('//layout.form_protection');

        $form->addSubmit('save', 'update.field');

        $form->onSuccess[] = function (Form $form): void {
            $this->savePreferences($form);
        };

        return $form;
    }

    private function savePreferences(Form $form): void
    {
        $userId = $this->userVIew->id;
        $command = ChangeNotificationPreferencesCommand::create(
            $userId->toString(),
            ...((array) $form->values->notifications),
        );

        try {
            $this->commandBus->dispatch($command);
        } catch (Throwable $e) {
            $this->logger->error((string) $e);
            $this->dispatchEvent(new NotificationPreferencesProcessingFailedEvent($e));

            return;
        }

        $this->dispatchEvent(new NotificationPreferencesUpdatedEvent($userId));
        $this->redrawControl();
    }
}
