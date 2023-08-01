<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentHistory;

use Nette\Bridges\ApplicationLatte\Template;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ConsentHistoryTemplate extends Template
{
    /** @var AbstractDomainEvent[]  */
    public array $events;

    /** @var ?int[] */
    public array $consentSettingsShortIdentifiers;
}
