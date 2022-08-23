<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Control\ExportForm;

use App\Web\Ui\Control;
use Nette\Application\UI\Form;
use App\Web\Utils\TranslatorUtils;
use Nette\Application\UI\Presenter;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use App\Bridge\Nette\Http\DownloadContentResponse;
use App\Application\DataProcessor\DataProcessFactory;
use App\Application\DataProcessor\Write\Writer\CsvWriter;
use App\Application\DataProcessor\Write\Writer\JsonWriter;
use App\Application\DataProcessor\Exception\WriterException;
use App\Application\DataProcessor\Exception\DataReaderExceptionInterface;
use App\Web\AdminModule\Control\ExportForm\Event\SuccessfullyExportedEvent;
use App\Web\AdminModule\Control\ExportForm\Callback\ExportCallbackInterface;
use App\Web\AdminModule\Control\ExportForm\Event\ExportFormProcessingFailedEvent;

final class ExportFormControl extends Control
{
	use FormFactoryOptionsTrait;

	private const FORMAT_CSV = 'csv';
	private const FORMAT_JSON = 'json';

	private const FORMATS = [
		self::FORMAT_CSV,
		self::FORMAT_JSON,
	];

	private const CSV_SEPARATORS = [
		'comma' => ',',
		'semicolon' => ';',
		'tabulator' => "\t",
		'space' => ' ',
		'pipe' => '|',
	];

	private FormFactoryInterface $formFactory;

	private DataProcessFactory $dataProcessFactory;

	private ExportCallbackInterface $exportCallback;

	/**
	 * @param \App\Web\Ui\Form\FormFactoryInterface                                    $formFactory
	 * @param \App\Application\DataProcessor\DataProcessFactory                        $dataProcessFactory
	 * @param \App\Web\AdminModule\Control\ExportForm\Callback\ExportCallbackInterface $exportCallback
	 */
	public function __construct(FormFactoryInterface $formFactory, DataProcessFactory $dataProcessFactory, ExportCallbackInterface $exportCallback)
	{
		$this->formFactory = $formFactory;
		$this->dataProcessFactory = $dataProcessFactory;
		$this->exportCallback = $exportCallback;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create($this->getFormFactoryOptions());
		$translator = $this->getPrefixedTranslator();

		$form->setTranslator($this->getPrefixedTranslator());

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
			->setDefaultValue('comma')
			->setOption('id', $this->getUniqueId() . '-separator-container')
			->addConditionOn($form['format'], $form::EQUAL, self::FORMAT_CSV)
				->setRequired('separator.required');

		$form->addProtection('//layout.form_protection');

		$form->addSubmit('export', 'export.field')
			->setOption('no-spinner', TRUE);

		$form->onSuccess[] = function (Form $form): void {
			$this->export($form);
		};

		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 *
	 * @return void
	 * @throws \Nette\Application\AbortException
	 */
	private function export(Form $form): void
	{
		$values = $form->values;
		$format = $values->format;
		$options = [];
		$contentType = 'application/octet-stream';

		switch ($format) {
			case self::FORMAT_JSON:
				$options = [
					JsonWriter::OPTION_PRETTY => TRUE,
					JsonWriter::OPTION_UNESCAPED_UNICODE => TRUE,
				];
				$contentType = 'application/json';

				break;
			case self::FORMAT_CSV:
				$options = [
					CsvWriter::INCLUDE_BOM => TRUE,
					CsvWriter::OPTION_DELIMITER => self::CSV_SEPARATORS[$values->separator],
				];
				$contentType = 'text/csv';

				break;
		}

		try {
			$content = ($this->exportCallback)($this->dataProcessFactory, $format, $options);
		} catch (DataReaderExceptionInterface|WriterException $e) {
			$this->logger->error((string) $e);
			$this->dispatchEvent(new ExportFormProcessingFailedEvent($e));
			$form->addError('error.processing_fail');
			$this->redrawControl();

			return;
		}

		$this->dispatchEvent(new SuccessfullyExportedEvent());

		$filename = $this->exportCallback->name() . '.' . $format;
		$presenter = $this->getPresenter();

		assert($presenter instanceof Presenter);

		$presenter->sendResponse(new DownloadContentResponse($content, $filename, $contentType));
	}
}
