<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\NotificationPreferences;

use Throwable;
use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use App\ReadModel\User\UserView;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use App\Domain\User\ValueObject\NotificationType;
use App\Domain\User\Command\ChangeNotificationPreferencesCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Web\AdminModule\UserModule\Control\NotificationPreferences\Event\NotificationPreferencesUpdatedEvent;
use App\Web\AdminModule\UserModule\Control\NotificationPreferences\Event\NotificationPreferencesProcessingFailedEvent;

final class NotificationPreferencesControl extends Control
{
	use FormFactoryOptionsTrait;

	private UserView $userVIew;

	private FormFactoryInterface $formFactory;

	private CommandBusInterface $commandBus;

	/**
	 * @param \App\ReadModel\User\UserView                                     $userVIew
	 * @param \App\Web\Ui\Form\FormFactoryInterface                            $formFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 */
	public function __construct(UserView $userVIew, FormFactoryInterface $formFactory, CommandBusInterface $commandBus)
	{
		$this->userVIew = $userVIew;
		$this->formFactory = $formFactory;
		$this->commandBus = $commandBus;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create($this->getFormFactoryOptions());
		$translator = $this->getPrefixedTranslator();

		$form->setTranslator($translator);

		$form->addCheckboxList('notifications', 'notifications.field')
			->setItems(NotificationType::values(), FALSE)
			->checkDefaultValue(FALSE)
			->setDefaultValue($this->userVIew->notificationPreferences->toArray());

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('save', 'update.field');

		$form->onSuccess[] = function (Form $form): void {
			$this->savePreferences($form);
		};

		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 */
	private function savePreferences(Form $form): void
	{
		$userId = $this->userVIew->id;
		$command = ChangeNotificationPreferencesCommand::create(
			$userId->toString(),
			...((array) $form->values->notifications)
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
