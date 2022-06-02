<?php

declare(strict_types=1);

namespace App\Subscribers\PasswordRequest;

use App\Application\Mail\Address;
use App\Application\Mail\Message;
use App\Application\Mail\Command\SendMailCommand;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Event\EventHandlerInterface;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\Event\PasswordChangeCompleted;

final class SendPasswordChangedMail implements EventHandlerInterface
{
	private CommandBusInterface $commandBus;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 */
	public function __construct(CommandBusInterface $commandBus)
	{
		$this->commandBus = $commandBus;
	}

	/**
	 * @param \SixtyEightPublishers\ForgotPasswordBundle\Domain\Event\PasswordChangeCompleted $event
	 *
	 * @return void
	 */
	public function __invoke(PasswordChangeCompleted $event): void
	{
		$message = Message::create('default:password_has_been_reset')
			->withTo(Address::create($event->emailAddress()->value()))
			->withArguments([
				'emailAddress' => $event->emailAddress()->value(),
			]);

		$this->commandBus->dispatch(SendMailCommand::create($message), [
			new DispatchAfterCurrentBusStamp(),
		]);
	}
}
