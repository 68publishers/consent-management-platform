<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportDetail;

use App\ReadModel\Import\ImportView;
use Nette\Bridges\ApplicationLatte\Template;

final class ImportDetailTemplate extends Template
{
	public ImportView $importView;
}
