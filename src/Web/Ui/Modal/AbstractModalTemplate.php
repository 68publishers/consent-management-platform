<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal;

use Nette\Bridges\ApplicationLatte\Template;

abstract class AbstractModalTemplate extends Template
{
	public string $modalName;

	public string $layout;
}
