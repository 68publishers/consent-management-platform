<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CrawlerModule\Control\ScenarioDetail;

use App\Web\Ui\Modal\AbstractModalTemplate;
use Nette\Security\User;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ScenarioResponse;
use Throwable;

final class ScenarioDetailModalTemplate extends AbstractModalTemplate
{
    public string $scenarioId;

    public ?ScenarioResponse $scenarioResponse = null;

    public ?Throwable $responseError = null;

    public User $user;
}
