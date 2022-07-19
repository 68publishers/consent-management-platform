<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Presenter;

use App\ReadModel\User\UserView;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\AdminModule\Presenter\AdminPresenter;
use App\Application\Localization\ApplicationDateTimeZone;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\UserBundle\Bridge\Nette\Security\Identity;
use App\Web\AdminModule\ProfileModule\Control\PasswordChange\PasswordChangeControl;
use App\Web\AdminModule\ProfileModule\Control\BasicInformation\BasicInformationControl;
use App\Web\AdminModule\ProfileModule\Control\PasswordChange\Event\PasswordChangedEvent;
use App\Web\AdminModule\ProfileModule\Control\PasswordChange\Event\PasswordChangeFailedEvent;
use App\Web\AdminModule\ProfileModule\Control\BasicInformation\Event\BasicInformationUpdatedEvent;
use App\Web\AdminModule\UserModule\Control\NotificationPreferences\NotificationPreferencesControl;
use App\Web\AdminModule\ProfileModule\Control\PasswordChange\PasswordChangeControlFactoryInterface;
use App\Web\AdminModule\ProfileModule\Control\BasicInformation\BasicInformationControlFactoryInterface;
use App\Web\AdminModule\ProfileModule\Control\BasicInformation\Event\BasicInformationUpdateFailedEvent;
use App\Web\AdminModule\UserModule\Control\NotificationPreferences\Event\NotificationPreferencesUpdatedEvent;
use App\Web\AdminModule\UserModule\Control\NotificationPreferences\NotificationPreferencesControlFactoryInterface;
use App\Web\AdminModule\UserModule\Control\NotificationPreferences\Event\NotificationPreferencesProcessingFailedEvent;

final class SettingsPresenter extends AdminPresenter
{
	private BasicInformationControlFactoryInterface $basicInformationControlFactory;

	private PasswordChangeControlFactoryInterface $passwordChangeControlFactory;

	private NotificationPreferencesControlFactoryInterface $notificationPreferencesControlFactory;

	/**
	 * @param \App\Web\AdminModule\ProfileModule\Control\BasicInformation\BasicInformationControlFactoryInterface            $basicInformationControlFactory
	 * @param \App\Web\AdminModule\ProfileModule\Control\PasswordChange\PasswordChangeControlFactoryInterface                $passwordChangeControlFactory
	 * @param \App\Web\AdminModule\UserModule\Control\NotificationPreferences\NotificationPreferencesControlFactoryInterface $notificationPreferencesControlFactory
	 */
	public function __construct(BasicInformationControlFactoryInterface $basicInformationControlFactory, PasswordChangeControlFactoryInterface $passwordChangeControlFactory, NotificationPreferencesControlFactoryInterface $notificationPreferencesControlFactory)
	{
		parent::__construct();

		$this->basicInformationControlFactory = $basicInformationControlFactory;
		$this->passwordChangeControlFactory = $passwordChangeControlFactory;
		$this->notificationPreferencesControlFactory = $notificationPreferencesControlFactory;
	}

	/**
	 * @return \App\Web\AdminModule\ProfileModule\Control\BasicInformation\BasicInformationControl
	 * @throws \SixtyEightPublishers\UserBundle\Application\Exception\IdentityException
	 */
	protected function createComponentBasicInformation(): BasicInformationControl
	{
		$identity = $this->getUser()->getIdentity();
		assert($identity instanceof Identity);

		$control = $this->basicInformationControlFactory->create($identity->data());

		$control->setFormFactoryOptions([
			FormFactoryInterface::OPTION_AJAX => TRUE,
		]);

		$control->addEventListener(BasicInformationUpdatedEvent::class, function (BasicInformationUpdatedEvent $event) {
			$this->subscribeFlashMessage(FlashMessage::success('basic_information_edited'));

			if ($event->oldProfile() !== $event->newProfile()) {
				$this->redirect('this');
			}

			$identity = $this->getIdentity();
			$identity->reload();

			$data = $identity->data();
			assert($data instanceof UserView);

			ApplicationDateTimeZone::set($data->timezone);

			$this->redrawControl('timezone-desktop');
			$this->redrawControl('timezone-mobile');
			$this->redrawControl('profile-menu');
		});

		$control->addEventListener(BasicInformationUpdateFailedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::error('basic_information_edit_failed'));
		});

		return $control;
	}

	/**
	 * @return \App\Web\AdminModule\ProfileModule\Control\PasswordChange\PasswordChangeControl
	 * @throws \SixtyEightPublishers\UserBundle\Application\Exception\IdentityException
	 */
	protected function createComponentPasswordChange(): PasswordChangeControl
	{
		$identity = $this->getUser()->getIdentity();
		assert($identity instanceof Identity);

		$control = $this->passwordChangeControlFactory->create($identity->data());

		$control->setFormFactoryOptions([
			FormFactoryInterface::OPTION_AJAX => TRUE,
		]);

		$control->addEventListener(PasswordChangedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::success('password_changed'));
		});

		$control->addEventListener(PasswordChangeFailedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::error('password_change_failed'));
		});

		return $control;
	}

	/**
	 * @return \App\Web\AdminModule\UserModule\Control\NotificationPreferences\NotificationPreferencesControl
	 * @throws \SixtyEightPublishers\UserBundle\Application\Exception\IdentityException
	 */
	protected function createComponentNotificationPreferences(): NotificationPreferencesControl
	{
		$identity = $this->getUser()->getIdentity();
		assert($identity instanceof Identity);

		$control = $this->notificationPreferencesControlFactory->create($identity->data());

		$control->setFormFactoryOptions([
			FormFactoryInterface::OPTION_AJAX => TRUE,
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
