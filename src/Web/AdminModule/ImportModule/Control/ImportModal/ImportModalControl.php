<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportModal;

use Nette\InvalidStateException;
use App\ReadModel\Import\ImportView;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Modal\AbstractModalControl;
use App\Domain\Import\ValueObject\ImportId;
use App\ReadModel\Import\GetImportByIdQuery;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\Web\AdminModule\ImportModule\Control\ImportForm\ImportFormControl;
use App\Web\AdminModule\ImportModule\Control\ImportDetail\ImportDetailControl;
use App\Web\AdminModule\ImportModule\Control\ImportForm\Event\ImportEndedEvent;
use App\Web\AdminModule\ImportModule\Control\ImportModal\Event\ShowingImportDetailEvent;
use App\Web\AdminModule\ImportModule\Control\ImportForm\ImportFormControlFactoryInterface;
use App\Web\AdminModule\ImportModule\Control\ImportDetail\ImportDetailControlFactoryInterface;

final class ImportModalControl extends AbstractModalControl
{
	/** @persistent */
	public string $importId  = '';

	private ImportFormControlFactoryInterface $importFormControlFactory;

	private ImportDetailControlFactoryInterface $importDetailControlFactory;

	private QueryBusInterface $queryBus;

	private ?string $strictImportType;

	private ?ImportView $importView = NULL;

	/**
	 * @param \App\Web\AdminModule\ImportModule\Control\ImportForm\ImportFormControlFactoryInterface     $importFormControlFactory
	 * @param \App\Web\AdminModule\ImportModule\Control\ImportDetail\ImportDetailControlFactoryInterface $importDetailControlFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface                             $queryBus
	 * @param string|NULL                                                                                $strictImportType
	 */
	public function __construct(ImportFormControlFactoryInterface $importFormControlFactory, ImportDetailControlFactoryInterface $importDetailControlFactory, QueryBusInterface $queryBus, ?string $strictImportType = NULL)
	{
		$this->importFormControlFactory = $importFormControlFactory;
		$this->importDetailControlFactory = $importDetailControlFactory;
		$this->queryBus = $queryBus;
		$this->strictImportType = $strictImportType;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->importView = $this->importView();
	}

	/**
	 * @return \App\Web\AdminModule\ImportModule\Control\ImportForm\ImportFormControl
	 */
	protected function createComponentForm(): ImportFormControl
	{
		$control = $this->importFormControlFactory->create($this->strictImportType);

		$control->setFormFactoryOptions([
			FormFactoryInterface::OPTION_AJAX => TRUE,
		]);

		$control->addEventListener(ImportEndedEvent::class, function (ImportEndedEvent $event): void {
			$this->importId = $event->importState()->id;
			$this->redrawControl();

			$this->dispatchEvent(new ShowingImportDetailEvent($event->importState()));
		});

		return $control;
	}

	/**
	 * @return \App\Web\AdminModule\ImportModule\Control\ImportDetail\ImportDetailControl
	 */
	protected function createComponentDetail(): ImportDetailControl
	{
		$importView = $this->importView();

		if (NULL === $importView) {
			throw new InvalidStateException('Missing import view.');
		}

		return $this->importDetailControlFactory->create($importView);
	}

	/**
	 * @return \App\ReadModel\Import\ImportView|NULL
	 */
	private function importView(): ?ImportView
	{
		if (NULL !== $this->importView) {
			return $this->importView;
		}

		if (empty($this->importId) || !ImportId::isValid($this->importId)) {
			return NULL;
		}

		return $this->importView = $this->queryBus->dispatch(GetImportByIdQuery::create($this->importId));
	}
}
