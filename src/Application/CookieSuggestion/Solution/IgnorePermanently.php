<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Solution;

final class IgnorePermanently implements SolutionInterface
{
    private bool $virtualSuggestion;

    public function __construct(bool $virtualSuggestion = false)
    {
        $this->virtualSuggestion = $virtualSuggestion;
    }

    public function getType(): string
    {
        return 'ignore_permanently';
    }

    public function getUniqueId(): string
    {
        return md5($this->getType());
    }

    public function getArguments(): array
    {
        return [
            'virtual_suggestion' => $this->virtualSuggestion,
        ];
    }

    public function getTranslatorArgs(): array
    {
        return [];
    }
}
