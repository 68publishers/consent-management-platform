<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use App\Domain\Project\ValueObject\Domain;
use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectDomainChanged extends AbstractDomainEvent
{
	private ProjectId $projectId;

	private Domain $domain;

	public static function create(ProjectId $projectId, Domain $domain): self
	{
		$event = self::occur($projectId->toString(), [
			'domain' => $domain->value(),
		]);

		$event->projectId = $projectId;
		$event->domain = $domain;

		return $event;
	}

	public function projectId(): ProjectId
	{
		return $this->projectId;
	}

	public function domain(): Domain
	{
		return $this->domain;
	}

	protected function reconstituteState(array $parameters): void
	{
		$this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
		$this->domain = Domain::fromValue($parameters['domain']);
	}
}
