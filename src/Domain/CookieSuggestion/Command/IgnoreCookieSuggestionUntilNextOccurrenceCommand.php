<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class IgnoreCookieSuggestionUntilNextOccurrenceCommand extends AbstractCommand
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
