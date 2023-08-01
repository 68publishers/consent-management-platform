<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportModal;

interface ImportModalControlFactoryInterface
{
    public function create(?string $strictImportType = null): ImportModalControl;
}
