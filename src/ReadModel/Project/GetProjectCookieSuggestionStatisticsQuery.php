<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `?ProjectCookieSuggestionsStatistics`
 */
final class GetProjectCookieSuggestionStatisticsQuery extends AbstractQuery
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
