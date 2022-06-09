<?php

declare(strict_types=1);

namespace App\Subscribers\PasswordRequest;

use DateTimeZone;
use App\ReadModel\User\UserView;
use App\Application\Mail\Address;
use App\Application\Mail\Message;
use Nette\Application\LinkGenerator;
use App\Application\Mail\Command\SendMailCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Event\EventHandlerInterface;
use SixtyEightPublishers\UserBundle\ReadModel\Query\GetUserByEmailAddressQuery;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\Event\PasswordChangeRequested;

final class SendPasswordChangeRequestedMail implements EventHandlerInterface
{
	private CommandBusInterface $commandBus;

	private QueryBusInterface $queryBus;

	private LinkGenerator $linkGenerator;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface   $queryBus
	 * @param \Nette\Application\LinkGenerator                                 $linkGenerator
	 */
	public function __construct(CommandBusInterface $commandBus, QueryBusInterface $queryBus, LinkGenerator $linkGenerator)
	{
		$this->commandBus = $commandBus;
		$this->queryBus = $queryBus;
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
		$userView = $this->queryBus->dispatch(GetUserByEmailAddressQuery::create($event->emailAddress()->value()));
		$locale = $userView instanceof UserView ? $userView->profileLocale->value() : NULL;
		$timezone = $userView instanceof UserView ? $userView->timezone : new DateTimeZone('UTC');

		$message = Message::create('default:password_change_requested', $locale)
			->withTo(Address::create($event->emailAddress()->value()))
			->withArguments([
				'emailAddress' => $event->emailAddress()->value(),
				'passwordRequestId' => $event->passwordRequestId()->toString(),
				'expireAt' => $event->expiredAt()->setTimezone($timezone)->format('j.n.Y H:i'),
				'resetLink' => $this->linkGenerator->link('Front:ResetPassword:', [
					'id' => $event->passwordRequestId()->toString(),
					'locale' => $locale,
				]),
			]);

		$this->commandBus->dispatch(SendMailCommand::create($message), [
			new DispatchAfterCurrentBusStamp(),
		]);
	}
}
