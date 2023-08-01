<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportDetail;

use App\ReadModel\Import\ImportView;
use App\Web\Ui\Modal\AbstractModalControl;

final class ImportDetailModalControl extends AbstractModalControl
{
    public function __construct(
        private readonly ImportView $importView,
        private readonly ImportDetailControlFactoryInterface $importDetailControlFactory,
    ) {}

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
