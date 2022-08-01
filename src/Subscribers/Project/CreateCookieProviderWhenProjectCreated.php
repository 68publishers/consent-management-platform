<?php

declare(strict_types=1);

namespace App\Subscribers\Project;

use App\Domain\Project\Event\ProjectCreated;
use App\ReadModel\CookieProvider\CookieProviderView;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use App\ReadModel\CookieProvider\GetCookieProviderByIdQuery;
use App\ReadModel\CookieProvider\GetCookieProviderByCodeQuery;
use App\Domain\CookieProvider\Command\CreateCookieProviderCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Event\EventHandlerInterface;

final class CreateCookieProviderWhenProjectCreated implements EventHandlerInterface
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
	 * @param \App\Domain\Project\Event\ProjectCreated $event
	 *
	 * @return void
	 */
	public function __invoke(ProjectCreated $event): void
	{
		$cookieProvider = $this->queryBus->dispatch(GetCookieProviderByIdQuery::create($event->cookieProviderId()->toString()));

		if ($cookieProvider instanceof CookieProviderView) {
			return;
		}

		$code = $event->code()->value();
		$cookieProvider = $this->queryBus->dispatch(GetCookieProviderByCodeQuery::create($code));

		# the code must be unique
		if ($cookieProvider instanceof CookieProviderView) {
			$code .= '.' . time();
		}

		$this->commandBus->dispatch(CreateCookieProviderCommand::create(
			$code,
			ProviderType::FIRST_PARTY,
			$event->name()->value(),
			'https://www.example.com',
			[],
			TRUE,
			TRUE,
			$event->cookieProviderId()->toString()
		));
	}
}
