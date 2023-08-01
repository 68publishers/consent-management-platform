<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class AddCookieSuggestionOccurrencesCommand extends AbstractCommand
{
    /**
     * @param array<int, CookieOccurrence> $occurrences
     */
    public static function create(
        string $cookieSuggestionId,
        array $occurrences,
    ): self {
        return self::fromParameters([
            'cookie_suggestion_id' => $cookieSuggestionId,
            'occurrences' => $occurrences,
        ]);
    }

    public function cookieSuggestionId(): string
    {
        return $this->getParam('cookie_suggestion_id');
    }

    /**
     * @return array<int, CookieOccurrence>
     */
    public function occurrences(): array
    {
        return $this->getParam('occurrences');
    }
}
