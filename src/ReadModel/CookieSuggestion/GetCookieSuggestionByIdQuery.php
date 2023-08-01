<?php

declare(strict_types=1);

namespace App\ReadModel\CookieSuggestion;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `?CookieSuggestion`
 */
final class GetCookieSuggestionByIdQuery extends AbstractQuery
{
    public static function create(string $cookieSuggestionId): self
    {
        return self::fromParameters([
            'cookie_suggestion_id' => $cookieSuggestionId,
        ]);
    }

    public function cookieSuggestionId(): string
    {
        return $this->getParam('cookie_suggestion_id');
    }
}
