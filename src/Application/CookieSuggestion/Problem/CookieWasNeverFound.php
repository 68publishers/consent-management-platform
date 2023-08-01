<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Problem;

use App\Application\CookieSuggestion\Solution\Solutions;

final class CookieWasNeverFound implements ProblemInterface
{
    public const TYPE = 'cookie_was_never_found';

    private Solutions $solutions;

    public function __construct(
        Solutions $solutions,
    ) {
        $this->solutions = $solutions;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getTranslatorArgs(): array
    {
        return [];
    }

    public function getSolutions(): Solutions
    {
        return $this->solutions;
    }
}
