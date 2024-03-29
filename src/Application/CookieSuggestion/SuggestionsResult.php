<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion;

use App\Application\CookieSuggestion\Problem\ProblemInterface;
use App\Application\CookieSuggestion\Suggestion\CookieOccurrence;
use App\Application\CookieSuggestion\Suggestion\IgnoredCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\MissingCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\ProblematicCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\SuggestionInterface;
use App\Application\CookieSuggestion\Suggestion\UnassociatedCookieSuggestion;

final class SuggestionsResult
{
    /** @var array<int, SuggestionInterface> */
    private array $suggestions = [];

    public function withSuggestion(SuggestionInterface $suggestion): self
    {
        $result = clone $this;
        $result->suggestions[] = $suggestion;

        return $result;
    }

    /**
     * @template T of SuggestionInterface
     *
     * @param class-string<T> $classname
     *
     * @return array<int, T>
     */
    public function getSuggestionsByType(string $classname): array
    {
        return array_values(
            array_filter(
                $this->suggestions,
                static fn (SuggestionInterface $suggestion): bool => is_a($suggestion, $classname),
            ),
        );
    }

    /**
     * @return array<int, SuggestionInterface>
     */
    public function getSuggestions(): array
    {
        return $this->suggestions;
    }

    public function getLatestOccurrence(): ?CookieOccurrence
    {
        $latestOccurrence = null;

        foreach ($this->suggestions as $suggestion) {
            $latest = $suggestion->getLatestOccurrence();

            if (null !== $latest && (null === $latestOccurrence || $latestOccurrence->lastFoundAt < $latest->lastFoundAt)) {
                $latestOccurrence = $latest;
            }
        }

        return $latestOccurrence;
    }

    public function getTotalNumberOfResolvableSuggestions(): int
    {
        $totalNumber = 0;

        foreach ($this->suggestions as $suggestion) {
            switch (true) {
                case $suggestion instanceof MissingCookieSuggestion:
                    $totalNumber += !empty($suggestion->getSolutions()->all()) ? 1 : 0;

                    break;
                case $suggestion instanceof UnassociatedCookieSuggestion:
                    $totalNumber += !empty($suggestion->getSolutions()->all()) ? 1 : 0;

                    break;
                case $suggestion instanceof ProblematicCookieSuggestion:
                    $totalNumber += array_sum(
                        array_map(
                            static fn (ProblemInterface $problem): int => !empty($problem->getSolutions()->all()) ? 1 : 0,
                            $suggestion->getProblems(),
                        ),
                    );

                    break;
                case $suggestion instanceof IgnoredCookieSuggestion:
                    $totalNumber += !empty($suggestion->getSolutions()->all()) ? 1 : 0;

                    break;
            }
        }

        return $totalNumber;
    }
}
