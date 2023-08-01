<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportDetail;

use App\ReadModel\Import\ImportView;
use App\Web\Ui\Modal\AbstractModalControl;

final class ImportDetailModalControl extends AbstractModalControl
{
    private ImportView $importView;

    private ImportDetailControlFactoryInterface $importDetailControlFactory;

    public function __construct(ImportView $importView, ImportDetailControlFactoryInterface $importDetailControlFactory)
    {
        $this->importView = $importView;
        $this->importDetailControlFactory = $importDetailControlFactory;
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof ImportDetailModalTemplate);

        $template->importView = $this->importView;
    }

    protected function createComponentDetail(): ImportDetailControl
    {
        return $this->importDetailControlFactory->create($this->importView);
    }
}
