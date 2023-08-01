<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioSchedulerForm;

use Nette\Bridges\ApplicationLatte\Template;
use SixtyEightPublishers\CrawlerClient\Exception\ControllerResponseExceptionInterface;

final class ScenarioSchedulerFormTemplate extends Template
{
    public ?ControllerResponseExceptionInterface $responseException = null;
}
