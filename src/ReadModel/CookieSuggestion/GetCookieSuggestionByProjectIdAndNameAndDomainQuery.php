<?php

declare(strict_types=1);

namespace App\ReadModel\CookieSuggestion;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `?CookieSuggestion`
 */
final class GetCookieSuggestionByProjectIdAndNameAndDomainQuery extends AbstractQuery
{
    public static function create(string $projectId, string $name, string $domain): self
    {
        return self::fromParameters([
            'project_id' => $projectId,
            'name' => $name,
            'domain' => $domain,
        ]);
    }

    public function projectId(): string
    {
        return $this->getParam('project_id');
    }

    public function name(): string
    {
        return $this->getParam('name');
    }

    public function domain(): string
    {
        return $this->getParam('domain');
    }
}
