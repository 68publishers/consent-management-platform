<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Problem;

use App\Application\CookieSuggestion\Solution\Solutions;

interface ProblemInterface
{
    public function getType(): string;

    /**
     * @return array<string, string|numeric>
     */
    public function getTranslatorArgs(): array;

    public function getSolutions(): Solutions;
}
