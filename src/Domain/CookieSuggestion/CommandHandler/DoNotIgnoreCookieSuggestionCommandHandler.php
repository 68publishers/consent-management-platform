<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion\CommandHandler;

use App\Domain\CookieSuggestion\ValueObject\CookieSuggestionId;
use App\Domain\CookieSuggestion\CookieSuggestionRepositoryInterface;
use App\Domain\CookieSuggestion\Command\DoNotIgnoreCookieSuggestionCommand;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class DoNotIgnoreCookieSuggestionCommandHandler implements CommandHandlerInterface
{
	private CookieSuggestionRepositoryInterface $cookieSuggestionRepository;

	public function __construct(
		CookieSuggestionRepositoryInterface $cookieSuggestionRepository
	) {
		$this->cookieSuggestionRepository = $cookieSuggestionRepository;
	}

	public function __invoke(DoNotIgnoreCookieSuggestionCommand $command): void
	{
		$cookieSuggestion = $this->cookieSuggestionRepository->get(CookieSuggestionId::fromString($command->cookieSuggestionId()));

		$cookieSuggestion->doNotIgnore();

		$this->cookieSuggestionRepository->save($cookieSuggestion);
	}
}
