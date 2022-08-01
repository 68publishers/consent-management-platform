<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use App\Domain\Project\ValueObject\Code;
use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class ProjectPermissionView extends AbstractView
{
	public ProjectId $projectId;

	public Code $projectCode;

	public bool $permission;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'project_id' => $this->projectId->toString(),
			'project_code' => $this->projectCode->value(),
			'permission' => $this->permission,
		];
	}
}
