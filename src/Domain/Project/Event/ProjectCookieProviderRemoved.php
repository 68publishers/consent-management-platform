<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectCookieProviderRemoved extends AbstractDomainEvent
{
	private ProjectId $projectId;

	private CookieProviderId $cookieProviderId;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId               $projectId
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 *
	 * @return static
	 */
	public static function create(ProjectId $projectId, CookieProviderId $cookieProviderId): self
	{
		$event = self::occur($projectId->toString(), [
			'cookie_provider_id' => $cookieProviderId->toString(),
		]);

		$event->projectId = $projectId;
		$event->cookieProviderId = $cookieProviderId;

		return $event;
	}

	/**
	 * @return \App\Domain\Project\ValueObject\ProjectId
	 */
	public function projectId(): ProjectId
	{
		return $this->projectId;
	}

	/**
	 * @return \App\Domain\CookieProvider\ValueObject\CookieProviderId
	 */
	public function cookieProviderId(): CookieProviderId
	{
		return $this->cookieProviderId;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
		$this->cookieProviderId = CookieProviderId::fromString($parameters['cookie_provider_id']);
	}
}
