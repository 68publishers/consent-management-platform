<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ProviderForm;

use Throwable;
use Nette\Utils\Html;
use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\TextInput;
use App\ReadModel\Project\ProjectView;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use App\Domain\CookieProvider\ValueObject\Code;
use App\Domain\CookieProvider\ValueObject\Purpose;
use App\Application\GlobalSettings\ValidLocalesProvider;
use App\ReadModel\CookieProvider\GetCookieProviderByIdQuery;
use App\Domain\CookieProvider\Exception\CodeUniquenessException;
use App\Domain\CookieProvider\Command\UpdateCookieProviderCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Web\AdminModule\ProjectModule\Control\ProviderForm\Event\ProviderUpdatedEvent;
use App\Web\AdminModule\ProjectModule\Control\ProviderForm\Event\ProviderFormProcessingFailedEvent;

final class ProviderFormControl extends Control
{
	use FormFactoryOptionsTrait;

	private ProjectView $projectView;

	private FormFactoryInterface $formFactory;

	private CommandBusInterface $commandBus;

	private QueryBusInterface $queryBus;

	private ValidLocalesProvider $validLocalesProvider;

	/**
	 * @param \App\ReadModel\Project\ProjectView                               $projectView
	 * @param \App\Web\Ui\Form\FormFactoryInterface                            $formFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface   $queryBus
	 * @param \App\Application\GlobalSettings\ValidLocalesProvider             $validLocalesProvider
	 */
	public function __construct(ProjectView $projectView, FormFactoryInterface $formFactory, CommandBusInterface $commandBus, QueryBusInterface $queryBus, ValidLocalesProvider $validLocalesProvider)
	{
		$this->projectView = $projectView;
		$this->formFactory = $formFactory;
		$this->commandBus = $commandBus;
		$this->queryBus = $queryBus;
		$this->validLocalesProvider = $validLocalesProvider;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create($this->getFormFactoryOptions());
		$translator = $this->getPrefixedTranslator();

		$form->setTranslator($translator);

		$form->addText('code', 'code.field')
			->setRequired('code.required')
			->addRule($form::MAX_LENGTH, 'code.rule_max_length', Code::MAX_LENGTH);

		$form->addText('name', 'name.field')
			->setRequired('name.required');

		$form->addText('link', 'link.field')
			->setRequired('link.required')
			->addRule($form::URL, 'link.rule_url');

		$namesContainer = $form->addContainer('purposes');

		foreach ($this->validLocalesProvider->getValidLocales($this->projectView->locales) as $locale) {
			$namesContainer->addTextArea($locale->code(), Html::fromText($translator->translate('purpose.field', ['code' => $locale->code(), 'name' => $locale->name()])), NULL, 4);
		}

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('save', 'update.field');

		$default = $this->queryBus->dispatch(GetCookieProviderByIdQuery::create($this->projectView->cookieProviderId->toString()));

		if (NULL !== $default) {
			$form->setDefaults([
				'code' => $default->code->value(),
				'name' => $default->name->value(),
				'link' => $default->link->value(),
				'purposes' => array_map(static fn (Purpose $purpose): string => $purpose->value(), $default->purposes),
			]);
		}

		$form->onSuccess[] = function (Form $form): void {
			$this->saveProvider($form);
		};

		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 */
	private function saveProvider(Form $form): void
	{
		$values = $form->values;
		$cookieProviderId = $this->projectView->cookieProviderId;

		$command = UpdateCookieProviderCommand::create($cookieProviderId->toString())
			->withCode($values->code)
			->withName($values->name)
			->withLink($values->link)
			->withPurposes((array) $values->purposes);

		try {
			$this->commandBus->dispatch($command);
		} catch (CodeUniquenessException $e) {
			$emailAddressField = $form->getComponent('code');
			assert($emailAddressField instanceof TextInput);

			$emailAddressField->addError('code.error.duplicated_value');

			return;
		} catch (Throwable $e) {
			$this->logger->error((string) $e);
			$this->dispatchEvent(new ProviderFormProcessingFailedEvent($e));

			return;
		}

		$this->dispatchEvent(new ProviderUpdatedEvent($cookieProviderId));
		$this->redrawControl();
	}
}
