<?php

declare(strict_types=1);

namespace App\Subscribers\CookieProvider;

use App\ReadModel\Cookie\CookieView;
use App\Domain\CookieProvider\CookieProvider;
use App\Domain\Cookie\Command\DeleteCookieCommand;
use App\Domain\Cookie\Exception\CookieNotFoundException;
use App\ReadModel\Cookie\FindCookiesByCookieProviderQuery;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Event\EventHandlerInterface;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AggregateDeleted;

final class DeleteCookiesWhenCookieProviderDeleted implements EventHandlerInterface
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
		if (CookieProvider::class !== $event->aggregateClassname()) {
			return;
		}

		$cookieViews = $this->queryBus->dispatch(FindCookiesByCookieProviderQuery::create($event->aggregateId()->toString()));

		foreach ($cookieViews as $cookieView) {
			assert($cookieView instanceof CookieView);

			try {
				$this->commandBus->dispatch(DeleteCookieCommand::create($cookieView->id->toString()));
			} catch (CookieNotFoundException $e) {
			}
		}
	}
}
