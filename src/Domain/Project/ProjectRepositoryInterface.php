<?php

declare(strict_types=1);

namespace App\Domain\Project;

use App\Domain\Project\ValueObject\ProjectId;

interface ProjectRepositoryInterface
{
	/**
	 * @param \App\Domain\Project\Project $project
	 *
	 * @return void
	 */
	public function save(Project $project): void;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $id
	 *
	 * @return \App\Domain\Project\Project
	 * @throws \App\Domain\Project\Exception\ProjectNotFoundException
	 */
	public function get(ProjectId $id): Project;
}
