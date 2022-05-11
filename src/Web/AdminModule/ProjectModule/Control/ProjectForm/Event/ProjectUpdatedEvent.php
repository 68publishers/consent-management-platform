<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ProjectForm\Event;

use Symfony\Contracts\EventDispatcher\Event;
use App\Domain\Project\ValueObject\ProjectId;

final class ProjectUpdatedEvent extends Event
{
	private ProjectId $projectId;

	private string $oldCode;

	private string $newCode;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 * @param string                                    $oldCode
	 * @param string                                    $newCode
	 */
	public function __construct(ProjectId $projectId, string $oldCode, string $newCode)
	{
		$this->projectId = $projectId;
		$this->oldCode = $oldCode;
		$this->newCode = $newCode;
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
	public function oldCode(): string
	{
		return $this->oldCode;
	}

	/**
	 * @return string
	 */
	public function newCode(): string
	{
		return $this->newCode;
	}
}
