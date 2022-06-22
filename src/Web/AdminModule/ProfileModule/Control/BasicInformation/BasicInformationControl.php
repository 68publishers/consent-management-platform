<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Control\BasicInformation;

use Throwable;
use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use App\ReadModel\User\UserView;
use App\Web\Ui\Form\FormFactoryInterface;
use Nepada\FormRenderer\TemplateRenderer;
use App\Application\Localization\Profiles;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use App\Application\Localization\ApplicationDateTimeZone;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\UserBundle\Domain\Command\UpdateUserCommand;
use App\Web\AdminModule\ProfileModule\Control\BasicInformation\Event\BasicInformationUpdatedEvent;
use App\Web\AdminModule\ProfileModule\Control\BasicInformation\Event\BasicInformationUpdateFailedEvent;

final class BasicInformationControl extends Control
{
	use FormFactoryOptionsTrait;

	private FormFactoryInterface $formFactory;

	private CommandBusInterface $commandBus;

	private Profiles $profiles;

	private UserView $userView;

	/**
	 * @param \App\Web\Ui\Form\FormFactoryInterface                            $formFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \App\Application\Localization\Profiles                           $profiles
	 * @param \App\ReadModel\User\UserView                                     $userView
	 */
	public function __construct(FormFactoryInterface $formFactory, CommandBusInterface $commandBus, Profiles $profiles, UserView $userView)
	{
		$this->formFactory = $formFactory;
		$this->commandBus = $commandBus;
		$this->profiles = $profiles;
		$this->userView = $userView;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->getTemplate()->userView = $this->userView;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create($this->getFormFactoryOptions());
		$renderer = $form->getRenderer();
		assert($renderer instanceof TemplateRenderer);

		$form->setTranslator($this->getPrefixedTranslator());
		$renderer->importTemplate(__DIR__ . '/templates/form.imports.latte');
		$renderer->getTemplate()->profiles = $this->profiles;

		$form->addText('firstname', 'firstname.field')
			->setRequired('firstname.required');

		$form->addText('surname', 'surname.field')
			->setRequired('surname.required');

		$profiles = [];
		foreach ($this->profiles->all() as $profile) {
			$profiles[$profile->locale()] = $profile->name();
		}

		$form->addSelect('profile', 'profile.field', $profiles)
			->setRequired('profile.required')
			->setTranslator(NULL);

		$form->addSelect('timezone', 'timezone.field')
			->setItems(ApplicationDateTimeZone::all(), FALSE)
			->setRequired('timezone.required')
			->setTranslator(NULL)
			->setOption('searchbar', TRUE);

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('save', 'save.field');

		$form->setDefaults([
			'firstname' => $this->userView->name->firstname(),
			'surname' => $this->userView->name->surname(),
			'profile' => $this->userView->profileLocale->value(),
			'timezone' => $this->userView->timezone->getName(),
		]);

		$form->onSuccess[] = function (Form $form): void {
			$this->saveBasicInformation($form);
		};

		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 */
	private function saveBasicInformation(Form $form): void
	{
		$values = $form->values;
		$command = UpdateUserCommand::create($this->userView->id->toString())
			->withFirstname($values->firstname)
			->withSurname($values->surname)
			->withParam('profile', $values->profile)
			->withParam('timezone', $values->timezone);

		try {
			$this->commandBus->dispatch($command);
		} catch (Throwable $e) {
			$this->logger->error((string) $e);
			$this->dispatchEvent(new BasicInformationUpdateFailedEvent($e));

			return;
		}

		$this->dispatchEvent(new BasicInformationUpdatedEvent($this->userView->id, $this->userView->profileLocale->value(), $values->profile));
		$this->redrawControl();
	}
}
