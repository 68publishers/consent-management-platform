<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportModal;

use App\Domain\Import\ValueObject\ImportId;
use App\ReadModel\Import\GetImportByIdQuery;
use App\ReadModel\Import\ImportView;
use App\Web\AdminModule\ImportModule\Control\ImportDetail\ImportDetailControl;
use App\Web\AdminModule\ImportModule\Control\ImportDetail\ImportDetailControlFactoryInterface;
use App\Web\AdminModule\ImportModule\Control\ImportForm\Event\ImportEndedEvent;
use App\Web\AdminModule\ImportModule\Control\ImportForm\ImportFormControl;
use App\Web\AdminModule\ImportModule\Control\ImportForm\ImportFormControlFactoryInterface;
use App\Web\AdminModule\ImportModule\Control\ImportModal\Event\ShowingImportDetailEvent;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Modal\AbstractModalControl;
use Nette\InvalidStateException;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;

final class ImportModalControl extends AbstractModalControl
{
    /** @persistent */
    public string $importId  = '';

    private ?ImportView $importView = null;

    public function __construct(
        private readonly ImportFormControlFactoryInterface $importFormControlFactory,
        private readonly ImportDetailControlFactoryInterface $importDetailControlFactory,
        private readonly QueryBusInterface $queryBus,
        private readonly ?string $strictImportType = null,
    ) {}

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof ImportModalTemplate);

        $template->importView = $this->importView();
    }

    protected function createComponentForm(): ImportFormControl
    {
        $control = $this->importFormControlFactory->create($this->strictImportType);

        $control->setFormFactoryOptions([
            FormFactoryInterface::OPTION_AJAX => true,
        ]);

        $control->addEventListener(ImportEndedEvent::class, function (ImportEndedEvent $event): void {
            $this->importId = $event->importState()->id;
            $this->redrawControl();

            $this->dispatchEvent(new ShowingImportDetailEvent($event->importState()));
        });

        return $control;
    }

    protected function createComponentDetail(): ImportDetailControl
    {
        $importView = $this->importView();

        if (null === $importView) {
            throw new InvalidStateException('Missing import view.');
        }

        return $this->importDetailControlFactory->create($importView);
    }

    private function importView(): ?ImportView
    {
        if (null !== $this->importView) {
            return $this->importView;
        }

        if (empty($this->importId) || !ImportId::isValid($this->importId)) {
            return null;
        }

        return $this->importView = $this->queryBus->dispatch(GetImportByIdQuery::create($this->importId));
    }
}
