<?php

declare(strict_types=1);

namespace App\Subscribers\PasswordRequest;

use App\Application\Mail\Address;
use App\Application\Mail\Message;
use Nette\Application\LinkGenerator;
use App\Application\Mail\Command\SendMailCommand;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Event\EventHandlerInterface;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\Event\PasswordChangeRequested;

final class SendPasswordChangeRequestedMail implements EventHandlerInterface
{
	private CommandBusInterface $commandBus;

	private LinkGenerator $linkGenerator;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \Nette\Application\LinkGenerator                                 $linkGenerator
	 */
	public function __construct(CommandBusInterface $commandBus, LinkGenerator $linkGenerator)
	{
		$this->commandBus = $commandBus;
		$this->linkGenerator = $linkGenerator;
	}

	/**
	 * @param \SixtyEightPublishers\ForgotPasswordBundle\Domain\Event\PasswordChangeRequested $event
	 *
	 * @return void
	 * @throws \Nette\Application\UI\InvalidLinkException
	 */
	public function __invoke(PasswordChangeRequested $event): void
	{
		$message = Message::create('default:password_change_requested')
			->withTo(Address::create($event->emailAddress()->value()))
			->withArguments([
				'emailAddress' => $event->emailAddress()->value(),
				'passwordRequestId' => $event->passwordRequestId()->toString(),
				'expireAt' => $event->expiredAt()->format('j.n.Y H:i'),
				'resetLink' => $this->linkGenerator->link('Front:ResetPassword:', ['id' => $event->passwordRequestId()->toString()]),
			]);

		$this->commandBus->dispatch(SendMailCommand::create($message), [
			new DispatchAfterCurrentBusStamp(),
		]);
	}
}
