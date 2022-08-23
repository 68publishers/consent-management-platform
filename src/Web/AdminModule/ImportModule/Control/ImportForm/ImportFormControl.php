<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportForm;

use Throwable;
use App\Web\Ui\Control;
use Nette\Http\FileUpload;
use Nette\Application\UI\Form;
use App\Web\Utils\TranslatorUtils;
use App\Application\Import\ImportOptions;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Application\Import\RunnerInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use App\Application\Import\Helper\KnownDescriptors;
use App\Application\DataProcessor\Read\Reader\CsvReader;
use App\Application\DataProcessor\Read\DataReaderFactoryInterface;
use SixtyEightPublishers\UserBundle\Bridge\Nette\Security\Identity;
use App\Web\AdminModule\ImportModule\Control\ImportForm\Event\ImportEndedEvent;
use App\Web\AdminModule\ImportModule\Control\ImportForm\Event\ImportFormProcessingFailedEvent;

final class ImportFormControl extends Control
{
	use FormFactoryOptionsTrait;

	private const FORMAT_CSV = 'csv';
	private const FORMAT_JSON = 'json';

	private const FORMATS = [
		self::FORMAT_CSV,
		self::FORMAT_JSON,
	];

	private const CSV_SEPARATORS = [
		'auto' => NULL,
		'comma' => ',',
		'semicolon' => ';',
		'tabulator' => "\t",
		'space' => ' ',
		'pipe' => '|',
	];

	private FormFactoryInterface $formFactory;

	private RunnerInterface $runner;

	private DataReaderFactoryInterface $dataReaderFactory;

	/**
	 * @param \App\Web\Ui\Form\FormFactoryInterface                          $formFactory
	 * @param \App\Application\Import\RunnerInterface                        $runner
	 * @param \App\Application\DataProcessor\Read\DataReaderFactoryInterface $dataReaderFactory
	 */
	public function __construct(FormFactoryInterface $formFactory, RunnerInterface $runner, DataReaderFactoryInterface $dataReaderFactory)
	{
		$this->formFactory = $formFactory;
		$this->runner = $runner;
		$this->dataReaderFactory = $dataReaderFactory;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create($this->getFormFactoryOptions());
		$translator = $this->getPrefixedTranslator();

		$form->setTranslator($this->getPrefixedTranslator());

		$form->addSelect('type', 'type.field')
			->setPrompt('-------')
			->setItems(array_combine(
				KnownDescriptors::ALL,
				TranslatorUtils::translateArray($translator, '//imports.name.', KnownDescriptors::ALL)
			))
			->setTranslator(NULL)
			->setRequired('type.required');

		$form->addSelect('format', 'format.field')
			->setItems(array_combine(
				self::FORMATS,
				TranslatorUtils::translateArray($translator, 'format.item.', self::FORMATS)
			))
			->setTranslator(NULL)
			->setRequired('format.required')
			->setDefaultValue(self::FORMAT_CSV)
			->addCondition($form::EQUAL, self::FORMAT_CSV)
				->toggle('#' . $this->getUniqueId() . '-separator-container');

		$form->addSelect('separator', 'separator.field')
			->setItems(array_combine(
				array_keys(self::CSV_SEPARATORS),
				TranslatorUtils::translateArray($translator, 'separator.item.', array_keys(self::CSV_SEPARATORS))
			))
			->setTranslator(NULL)
			->setDefaultValue('auto')
			->setOption('id', $this->getUniqueId() . '-separator-container')
			->addConditionOn($form['format'], $form::EQUAL, self::FORMAT_CSV)
				->setRequired('separator.required');

		$form->addUpload('file', 'file.field')
			->setRequired('file.required')
			->addConditionOn($form['format'], $form::EQUAL, self::FORMAT_CSV)
				->addRule($form::MIME_TYPE, 'file.rule.mime_type.csv', ['text/csv', 'text/plain'])
				->endCondition()
			->addConditionOn($form['format'], $form::EQUAL, self::FORMAT_JSON)
				->addRule($form::MIME_TYPE, 'file.rule.mime_type.json', ['application/json', 'text/plain'])
				->endCondition();

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('import', 'import.field');

		$form->onSuccess[] = function (Form $form): void {
			$this->import($form);
		};

		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 */
	private function import(Form $form): void
	{
		$values = $form->values;

		try {
			$type = $values->type;
			$format = $values->format;
			$filename = $values->file->getTemporaryFile();

			$file = $values->file;
			assert($file instanceof FileUpload);

			$options = self::FORMAT_CSV === $format ? [
				CsvReader::OPTION_HAS_HEADER => TRUE,
				CsvReader::OPTION_DELIMITER => self::CSV_SEPARATORS[$values->separator],
			] : [];

			$reader = $this->dataReaderFactory->fromFile($format, $filename, $options);
			$options = ImportOptions::create($type)
				->withAuthor($this->getAuthor())
				->withBatchSize(50);

			$state = $this->runner->run($reader, $options);
		} catch (Throwable $e) {
			$this->logger->error((string) $e);
			$this->dispatchEvent(new ImportFormProcessingFailedEvent($e));
			$form->addError('error.processing_fail');
			$this->redrawControl();

			return;
		}

		$this->dispatchEvent(new ImportEndedEvent($state));
		$this->redrawControl();
	}

	/**
	 * @return string
	 * @throws \SixtyEightPublishers\UserBundle\Application\Exception\IdentityException
	 */
	private function getAuthor(): string
	{
		$user = $this->getUser();

		if (!$user->isLoggedIn()) {
			return 'unknown';
		}

		$identity = $user->getIdentity();
		assert($identity instanceof Identity);

		return $identity->data()->name->name();
	}
}
