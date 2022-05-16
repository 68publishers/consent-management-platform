<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal;

use Nette\Bridges\ApplicationLatte\Template;

final class ModalsTemplate extends Template
{
	public HtmlId $elementId;

	public string $payload;
}
