<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioDetail;

use Throwable;
use Nette\Security\User;
use App\Web\Ui\Modal\AbstractModalTemplate;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ScenarioResponse;

final class ScenarioDetailModalTemplate extends AbstractModalTemplate
{
	public string $scenarioId;

	public ?ScenarioResponse $scenarioResponse = NULL;

	public ?Throwable $responseError = NULL;

	public User $user;
}
