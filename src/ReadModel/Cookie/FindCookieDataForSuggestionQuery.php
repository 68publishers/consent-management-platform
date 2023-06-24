<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `array<CookieDataForSuggestion>`
 */
final class FindCookieDataForSuggestionQuery extends AbstractQuery
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
