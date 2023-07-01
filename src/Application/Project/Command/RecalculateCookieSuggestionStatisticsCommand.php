<?php

declare(strict_types=1);

namespace App\Application\Project\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class RecalculateCookieSuggestionStatisticsCommand extends AbstractCommand
{
	/**
	 * @param non-empty-list<string> $projectIds
	 */
	public static function create(array $projectIds): self
	{
		return self::fromParameters([
			'project_ids' => $projectIds,
		]);
	}

	/**
	 * @return non-empty-list<string>
	 */
	public function projectIds(): array
	{
		return $this->getParam('project_ids');
	}
}
