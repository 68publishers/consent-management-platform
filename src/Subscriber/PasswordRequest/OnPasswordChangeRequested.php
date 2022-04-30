<?php

declare(strict_types=1);

namespace App\Subscriber\PasswordRequest;

use Nette\Application\LinkGenerator;
use SixtyEightPublishers\ArchitectureBundle\Event\EventHandlerInterface;
use SixtyEightPublishers\ForgotPasswordBundle\Domain\Event\PasswordChangeRequested;

final class OnPasswordChangeRequested implements EventHandlerInterface
{
	private LinkGenerator $linkGenerator;

	/**
	 * @param \Nette\Application\LinkGenerator $linkGenerator
	 */
	public function __construct(LinkGenerator $linkGenerator)
	{
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
		$link = $this->linkGenerator->link('Front:ResetPassword:', ['id' => $event->passwordRequestId()->toString()]);

		bdump($link);
	}
}
