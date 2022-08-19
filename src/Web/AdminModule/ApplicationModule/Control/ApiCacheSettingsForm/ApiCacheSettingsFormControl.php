<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Control\ApiCacheSettingsForm;

use Throwable;
use Nette\Utils\Html;
use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use App\Application\GlobalSettings\GlobalSettingsInterface;
use App\Domain\GlobalSettings\Command\PutApiCacheSettingsCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Web\AdminModule\ApplicationModule\Control\ApiCacheSettingsForm\Event\ApiCacheSettingsUpdatedEvent;
use App\Web\AdminModule\ApplicationModule\Control\ApiCacheSettingsForm\Event\ApiCacheSettingsUpdateFailedEvent;

final class ApiCacheSettingsFormControl extends Control
{
	use FormFactoryOptionsTrait;

	private FormFactoryInterface $formFactory;

	private CommandBusInterface $commandBus;

	private GlobalSettingsInterface $globalSettings;

	/**
	 * @param \App\Web\Ui\Form\FormFactoryInterface                            $formFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \App\Application\GlobalSettings\GlobalSettingsInterface          $globalSettings
	 */
	public function __construct(FormFactoryInterface $formFactory, CommandBusInterface $commandBus, GlobalSettingsInterface $globalSettings)
	{
		$this->formFactory = $formFactory;
		$this->commandBus = $commandBus;
		$this->globalSettings = $globalSettings;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create($this->getFormFactoryOptions());
		$translator = $this->getPrefixedTranslator();

		$form->setTranslator($translator);

		$form->addCheckbox('use_entity_tag', 'use_entity_tag.field')
			->setOption('description', Html::fromHtml($translator->translate('use_entity_tag.description')));

		$form->addCheckbox('cache_control_enabled', 'cache_control_enabled.field')
			->addCondition($form::EQUAL, TRUE)
				->toggle('#' . $this->getUniqueId() . '-max-age-container')
				->endCondition()
			->addConditionOn($form['use_entity_tag'], $form::EQUAL, TRUE)
				->setRequired('cache_control_enabled.required');

		$form->addText('max_age', 'max_age.field')
			->setOption('id', $this->getUniqueId() . '-max-age-container')
			->setOption('description', Html::fromHtml($translator->translate('max_age.description')))
			->addConditionOn($form['cache_control_enabled'], $form::EQUAL, TRUE)
				->setRequired('max_age.required')
				->addRule($form::INTEGER, 'max_age.rule.integer')
				->addRule($form::MIN, 'max_age.rule.min', 0);

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('save', 'save.field');

		$directives = $this->globalSettings->apiCache()->cacheControlDirectives();
		$maxAge = 0;

		foreach ($directives as $directive) {
			preg_match('/^max-age=(?<MAX_AGE>\d+)$/', $directive, $m);

			if (isset($m['MAX_AGE'])) {
				$maxAge = (int) $m['MAX_AGE'];

				break;
			}
		}

		$form->setDefaults([
			'cache_control_enabled' => !empty($directives),
			'max_age' => $maxAge,
			'use_entity_tag' => $this->globalSettings->apiCache()->useEntityTag(),
		]);

		$form->onSuccess[] = function (Form $form): void {
			$this->saveGlobalSettings($form);
		};

		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 */
	private function saveGlobalSettings(Form $form): void
	{
		$values = $form->values;
		$directives = !$values->cache_control_enabled ? [] : [
			'max-age=' . $values->max_age,
		];

		$command = PutApiCacheSettingsCommand::create($directives, $values->use_entity_tag);

		try {
			$this->commandBus->dispatch($command);
		} catch (Throwable $e) {
			$this->logger->error((string) $e);
			$this->dispatchEvent(new ApiCacheSettingsUpdateFailedEvent($e));

			return;
		}

		$this->dispatchEvent(new ApiCacheSettingsUpdatedEvent());
		$this->redrawControl();
	}
}
