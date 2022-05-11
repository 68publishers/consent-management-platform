<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Project\ValueObject\ProjectId;

final class UserHasProject
{
	private int $id;

	private User $user;

	private ProjectId $projectId;

	private function __construct()
	{
	}

	/**
	 * @param \App\Domain\User\User                     $user
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 *
	 * @return static
	 */
	public static function create(User $user, ProjectId $projectId): self
	{
		$association = new self();
		$association->user = $user;
		$association->projectId = $projectId;

		return $association;
	}

	/**
	 * @return \App\Domain\Project\ValueObject\ProjectId
	 */
	public function projectId(): ProjectId
	{
		return $this->projectId;
	}
}
