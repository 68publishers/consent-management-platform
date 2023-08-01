<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportDetail;

use App\ReadModel\Import\ImportView;
use App\Web\Ui\Modal\AbstractModalTemplate;

final class ImportDetailModalTemplate extends AbstractModalTemplate
{
    public ImportView $importView;
}
