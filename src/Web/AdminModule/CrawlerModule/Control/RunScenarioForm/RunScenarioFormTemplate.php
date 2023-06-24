<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\RunScenarioForm;

use Nette\Bridges\ApplicationLatte\Template;
use SixtyEightPublishers\CrawlerClient\Exception\ControllerResponseExceptionInterface;

final class RunScenarioFormTemplate extends Template
{
	public ?ControllerResponseExceptionInterface $responseException = NULL;
}
