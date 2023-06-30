<?php

declare(strict_types=1);

namespace App\Domain\Project\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class DeleteProjectCommand extends AbstractCommand
{
	public static function create(string $projectId): self
	{
		return self::fromParameters([
			'project_id' => $projectId,
		]);
	}

	public function projectId(): string
	{
		return $this->getParam('project_id');
	}
}
