<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use Nette\InvalidStateException;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Application\Acl\ProjectIntegrationResource;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\SmartNetteComponent\Annotation\IsAllowed;
use App\Web\AdminModule\ProjectModule\Control\TemplatesForm\TemplatesFormControl;
use App\Web\AdminModule\ProjectModule\Control\TemplatesForm\Event\TemplatesUpdatedEvent;
use App\Web\AdminModule\ProjectModule\Control\TemplatesForm\TemplatesFormControlFactoryInterface;
use App\Web\AdminModule\ProjectModule\Control\TemplatesForm\Event\TemplatesFormProcessingFailedEvent;

/**
 * @IsAllowed(resource=ProjectIntegrationResource::class, privilege=ProjectIntegrationResource::READ)
 */
final class IntegrationPresenter extends SelectedProjectPresenter
{
	private TemplatesFormControlFactoryInterface $templatesFormControlFactory;

	/**
	 * @param \App\Web\AdminModule\ProjectModule\Control\TemplatesForm\TemplatesFormControlFactoryInterface $templatesFormControlFactory
	 */
	public function __construct(TemplatesFormControlFactoryInterface $templatesFormControlFactory)
	{
		parent::__construct();

		$this->templatesFormControlFactory = $templatesFormControlFactory;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->appHost = $this->getHttpRequest()->getUrl()->getHostUrl();
	}

	/**
	 * @return \App\Web\AdminModule\ProjectModule\Control\TemplatesForm\TemplatesFormControl
	 */
	protected function createComponentTemplatesForm(): TemplatesFormControl
	{
		if (!$this->getUser()->isAllowed(ProjectIntegrationResource::class, ProjectIntegrationResource::UPDATE)) {
			throw new InvalidStateException('The user is not allowed to update project\'s templates.');
		}

		$control = $this->templatesFormControlFactory->create($this->projectView, $this->validLocalesProvider->withLocalesConfig($this->projectView->locales));

		$control->setFormFactoryOptions([
			FormFactoryInterface::OPTION_AJAX => TRUE,
		]);

		$control->addEventListener(TemplatesUpdatedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::success('templates_updated'));
		});

		$control->addEventListener(TemplatesFormProcessingFailedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::error('templates_updated_failed'));
		});

		return $control;
	}
}
