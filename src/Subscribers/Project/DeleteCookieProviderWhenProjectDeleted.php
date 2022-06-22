<?php

declare(strict_types=1);

namespace App\Subscribers\Project;

use App\Domain\Project\Project;
use App\ReadModel\Project\ProjectView;
use App\ReadModel\Project\GetProjectByIdQuery;
use App\Domain\CookieProvider\Command\DeleteCookieProviderCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Domain\CookieProvider\Exception\CookieProviderNotFoundException;
use SixtyEightPublishers\ArchitectureBundle\Event\EventHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AggregateDeleted;

final class DeleteCookieProviderWhenProjectDeleted implements EventHandlerInterface
{
	private CommandBusInterface $commandBus;

	private QueryBusInterface $queryBus;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface   $queryBus
	 */
	public function __construct(CommandBusInterface $commandBus, QueryBusInterface $queryBus)
	{
		$this->commandBus = $commandBus;
		$this->queryBus = $queryBus;
	}

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Domain\Event\AggregateDeleted $event
	 *
	 * @return void
	 */
	public function __invoke(AggregateDeleted $event): void
	{
		if (Project::class !== $event->aggregateClassname()) {
			return;
		}

		$projectView = $this->queryBus->dispatch(GetProjectByIdQuery::create($event->aggregateId()->toString()));

		if (!$projectView instanceof ProjectView) {
			return;
		}

		try {
			$this->commandBus->dispatch(DeleteCookieProviderCommand::create($projectView->cookieProviderId->toString()));
		} catch (CookieProviderNotFoundException $e) {
		}
	}
}
