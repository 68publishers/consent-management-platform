<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentHistory;

use App\Domain\GlobalSettings\ValueObject\Environment;
use Nette\Bridges\ApplicationLatte\Template;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ConsentHistoryTemplate extends Template
{
    /** @var array<AbstractDomainEvent> */
    public array $events;

    /** @var array<int> */
    public array $consentSettingsShortIdentifiers;

    /** @var array<string, Environment> */
    public array $environments;
}
