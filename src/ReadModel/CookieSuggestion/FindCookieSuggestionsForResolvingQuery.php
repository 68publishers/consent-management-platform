<?php

declare(strict_types=1);

namespace App\ReadModel\CookieSuggestion;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `array<CookieSuggestionForResolving>`
 */
final class FindCookieSuggestionsForResolvingQuery extends AbstractQuery
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
