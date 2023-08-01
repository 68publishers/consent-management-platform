<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportModal;

use App\ReadModel\Import\ImportView;
use App\Web\Ui\Modal\AbstractModalTemplate;

final class ImportModalTemplate extends AbstractModalTemplate
{
    public ?ImportView $importView = null;
}
