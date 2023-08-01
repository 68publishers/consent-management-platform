<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion\CommandHandler;

use App\Domain\CookieSuggestion\Command\IgnoreCookieSuggestionUntilNextOccurrenceCommand;
use App\Domain\CookieSuggestion\CookieSuggestionRepositoryInterface;
use App\Domain\CookieSuggestion\ValueObject\CookieSuggestionId;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class IgnoreCookieSuggestionUntilNextOccurrenceCommandHandler implements CommandHandlerInterface
{
    private CookieSuggestionRepositoryInterface $cookieSuggestionRepository;

    public function __construct(
        CookieSuggestionRepositoryInterface $cookieSuggestionRepository,
    ) {
        $this->cookieSuggestionRepository = $cookieSuggestionRepository;
    }

    public function __invoke(IgnoreCookieSuggestionUntilNextOccurrenceCommand $command): void
    {
        $cookieSuggestion = $this->cookieSuggestionRepository->get(CookieSuggestionId::fromString($command->cookieSuggestionId()));

        $cookieSuggestion->ignoreUntilNextOccurrence();

        $this->cookieSuggestionRepository->save($cookieSuggestion);
    }
}
