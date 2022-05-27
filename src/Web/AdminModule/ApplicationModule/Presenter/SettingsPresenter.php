<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Presenter;

use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\AdminModule\Presenter\AdminPresenter;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use App\Web\AdminModule\ApplicationModule\Control\GlobalSettingsForm\GlobalSettingsFormControl;
use App\Web\AdminModule\ApplicationModule\Control\GlobalSettingsForm\Event\GlobalSettingsUpdatedEvent;
use App\Web\AdminModule\ApplicationModule\Control\GlobalSettingsForm\Event\GlobalSettingsUpdateFailedEvent;
use App\Web\AdminModule\ApplicationModule\Control\GlobalSettingsForm\GlobalSettingsFormControlFactoryInterface;

final class SettingsPresenter extends AdminPresenter
{
	private GlobalSettingsFormControlFactoryInterface $globalSettingsFormControlFactory;

	/**
	 * @param \App\Web\AdminModule\ApplicationModule\Control\GlobalSettingsForm\GlobalSettingsFormControlFactoryInterface $globalSettingsFormControlFactory
	 */
	public function __construct(GlobalSettingsFormControlFactoryInterface $globalSettingsFormControlFactory)
	{
		parent::__construct();

		$this->globalSettingsFormControlFactory = $globalSettingsFormControlFactory;
	}

	/**
	 * @return \App\Web\AdminModule\ApplicationModule\Control\GlobalSettingsForm\GlobalSettingsFormControl
	 */
	protected function createComponentGlobalSettingsForm(): GlobalSettingsFormControl
	{
		$control = $this->globalSettingsFormControlFactory->create();

		$control->setFormFactoryOptions([
			FormFactoryInterface::OPTION_AJAX => TRUE,
		]);

		$control->addEventListener(GlobalSettingsUpdatedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::success('global_settings_updated'));
			$this->globalSettings->refresh();
			$this->redrawControl('before_content');
		});

		$control->addEventListener(GlobalSettingsUpdateFailedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::error('global_settings_update_failed'));
		});

		return $control;
	}
}
