<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentHistory;

use App\ReadModel\Consent\ConsentView;

interface ConsentHistoryModalControlFactoryInterface
{
    public function create(ConsentView $consentView): ConsentHistoryModalControl;
}
