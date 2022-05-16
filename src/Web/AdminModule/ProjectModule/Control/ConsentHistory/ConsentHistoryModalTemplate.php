<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentHistory;

use App\ReadModel\Consent\ConsentView;
use App\Web\Ui\Modal\AbstractModalTemplate;

final class ConsentHistoryModalTemplate extends AbstractModalTemplate
{
	public ConsentView $consentView;
}
