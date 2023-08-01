<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportDetail;

use App\ReadModel\Import\ImportView;

interface ImportDetailModalControlFactoryInterface
{
    public function create(ImportView $importView): ImportDetailModalControl;
}
