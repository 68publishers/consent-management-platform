<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractBatchedQuery;

/**
 * Returns `array<CookieApiView>`
 */
final class FindCookiesForApiQuery extends AbstractBatchedQuery
{
    public static function create(string $projectId, ?string $locale = null): self
    {
        return self::fromParameters([
            'project_id' => $projectId,
            'locale' => $locale,
        ]);
    }

    /**
     * @param array<string> $categoryCodes
     */
    public function withCategoryCodes(array $categoryCodes): self
    {
        return $this->withParam('category_codes', $categoryCodes);
    }

    public function projectId(): string
    {
        return $this->getParam('project_id');
    }

    public function locale(): ?string
    {
        return $this->getParam('locale');
    }

    /**
     * @return array<string>|NULL
     */
    public function categoryCodes(): ?array
    {
        return $this->getParam('category_codes');
    }
}
