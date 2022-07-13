<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use App\Domain\Project\ValueObject\Code;
use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class ProjectAccessibilityView extends AbstractView
{
	public ProjectId $projectId;

	public Code $projectCode;

	public bool $accessible;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'project_id' => $this->projectId->toString(),
			'project_code' => $this->projectCode->value(),
			'accessible' => $this->accessible,
		];
	}
}
