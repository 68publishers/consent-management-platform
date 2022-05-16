<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentHistory;

use Nette\Bridges\ApplicationLatte\Template;

final class ConsentHistoryTemplate extends Template
{
	/** @var \SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent[]  */
	public array $events;
}
