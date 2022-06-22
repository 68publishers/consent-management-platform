<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm;

use Throwable;
use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use App\ReadModel\Project\ProjectView;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use App\Domain\Project\Command\UpdateProjectCommand;
use App\ReadModel\CookieProvider\CookieProviderSelectOptionView;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\ReadModel\CookieProvider\FindCookieProviderSelectOptionsQuery;
use App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm\Event\OtherProvidersUpdatedEvent;
use App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm\Event\OtherProvidersFormProcessingFailedEvent;

final class OtherProvidersFormControl extends Control
{
	use FormFactoryOptionsTrait;

	private ProjectView $projectView;

	private FormFactoryInterface $formFactory;

	private CommandBusInterface $commandBus;

	private QueryBusInterface $queryBus;

	/**
	 * @param \App\ReadModel\Project\ProjectView                               $projectView
	 * @param \App\Web\Ui\Form\FormFactoryInterface                            $formFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface   $queryBus
	 */
	public function __construct(ProjectView $projectView, FormFactoryInterface $formFactory, CommandBusInterface $commandBus, QueryBusInterface $queryBus)
	{
		$this->projectView = $projectView;
		$this->formFactory = $formFactory;
		$this->commandBus = $commandBus;
		$this->queryBus = $queryBus;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create($this->getFormFactoryOptions());
		$translator = $this->getPrefixedTranslator();

		$form->setTranslator($translator);

		$form->addMultiSelect('cookie_providers', 'cookie_providers.field')
			->setItems($this->getCookieProviderOptions())
			->checkDefaultValue(FALSE)
			->setTranslator(NULL)
			->setOption('searchbar', TRUE)
			->setOption('tags', TRUE);

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('save', 'update.field');

		$form->setDefaults([
			'cookie_providers' => array_map(static fn (CookieProviderSelectOptionView $view): string => $view->id->toString(), $this->queryBus->dispatch(FindCookieProviderSelectOptionsQuery::byProject($this->projectView->id->toString()))),
		]);

		$form->onSuccess[] = function (Form $form): void {
			$this->saveProviders($form);
		};

		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 */
	private function saveProviders(Form $form): void
	{
		$values = $form->values;

		$command = UpdateProjectCommand::create($this->projectView->id->toString())
			->withCookieProviderIds($values->cookie_providers);

		try {
			$this->commandBus->dispatch($command);
		} catch (Throwable $e) {
			$this->logger->error((string) $e);
			$this->dispatchEvent(new OtherProvidersFormProcessingFailedEvent($e));

			return;
		}

		$this->dispatchEvent(new OtherProvidersUpdatedEvent());
		$this->redrawControl();
	}

	/**
	 * @return array
	 */
	private function getCookieProviderOptions(): array
	{
		$options = [];

		/** @var \App\ReadModel\CookieProvider\CookieProviderSelectOptionView $cookieProviderSelectOptionView */
		foreach ($this->queryBus->dispatch(FindCookieProviderSelectOptionsQuery::all()) as $cookieProviderSelectOptionView) {
			$options += $cookieProviderSelectOptionView->toOption();
		}

		return $options;
	}
}
