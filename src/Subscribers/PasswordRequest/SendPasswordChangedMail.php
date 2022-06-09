<?php

declare(strict_types=1);

namespace App\Subscribers\PasswordRequest;

use App\ReadModel\User\UserView;
use App\Application\Mail\Address;
use App\Application\Mail\Message;
use App\Application\Mail\Command\SendMailCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Event\EventHandlerInterface;
use SixtyEightPublishers\UserBundle\ReadModel\Query\GetUserByEmailAddressQuery;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\Event\PasswordChangeCompleted;

final class SendPasswordChangedMail implements EventHandlerInterface
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
	 * @param \SixtyEightPublishers\ForgotPasswordBundle\Domain\Event\PasswordChangeCompleted $event
	 *
	 * @return void
	 */
	public function __invoke(PasswordChangeCompleted $event): void
	{
		$userView = $this->queryBus->dispatch(GetUserByEmailAddressQuery::create($event->emailAddress()->value()));
		$locale = $userView instanceof UserView ? $userView->profileLocale->value() : NULL;

		$message = Message::create('default:password_has_been_reset', $locale)
			->withTo(Address::create($event->emailAddress()->value()))
			->withArguments([
				'emailAddress' => $event->emailAddress()->value(),
			]);

		$this->commandBus->dispatch(SendMailCommand::create($message), [
			new DispatchAfterCurrentBusStamp(),
		]);
	}
}
