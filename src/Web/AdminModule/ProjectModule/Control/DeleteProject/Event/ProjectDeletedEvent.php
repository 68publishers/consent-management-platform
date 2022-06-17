<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\DeleteProject\Event;

use Symfony\Contracts\EventDispatcher\Event;
use App\Domain\Project\ValueObject\ProjectId;

final class ProjectDeletedEvent extends Event
{
	private ProjectId $projectId;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 */
	public function __construct(ProjectId $projectId)
	{
		$this->projectId = $projectId;
	}

	/**
	 * @return \App\Domain\Project\ValueObject\ProjectId
	 */
	public function projectId(): ProjectId
	{
		return $this->projectId;
	}
}
