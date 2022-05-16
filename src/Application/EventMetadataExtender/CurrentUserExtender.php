<?php

declare(strict_types=1);

namespace App\Application\EventMetadataExtender;

use Nette\Security\User as NetteUser;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;
use SixtyEightPublishers\ArchitectureBundle\EventStore\EventMetadataExtenderInterface;

final class CurrentUserExtender implements EventMetadataExtenderInterface
{
	public const KEY_USER_ID = 'user_id';

	private NetteUser $user;

	/**
	 * @param \Nette\Security\User $user
	 */
	public function __construct(NetteUser $user)
	{
		$this->user = $user;
	}

	/**
	 * {@inheritDoc}
	 */
	public function extendMetadata(AbstractDomainEvent $event): AbstractDomainEvent
	{
		if ($this->user->isLoggedIn()) {
			$event = $event->withMetadata([
				self::KEY_USER_ID => (string) $this->user->id,
			], TRUE);
		}

		return $event;
	}
}
