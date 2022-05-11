<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event;

use Symfony\Contracts\EventDispatcher\Event;
use App\Domain\Project\ValueObject\ProjectId;

final class ProjectCreatedEvent extends Event
{
	private ProjectId $projectId;

	private string $code;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 * @param string                                    $code
	 */
	public function __construct(ProjectId $projectId, string $code)
	{
		$this->projectId = $projectId;
		$this->code = $code;
	}

	/**
	 * @return \App\Domain\Project\ValueObject\ProjectId
	 */
	public function projectId(): ProjectId
	{
		return $this->projectId;
	}

	/**
	 * @return string
	 */
	public function code(): string
	{
		return $this->code;
	}
}
