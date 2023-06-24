<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion\CommandHandler;

use Exception;
use App\Domain\CookieSuggestion\ValueObject\CookieSuggestionId;
use App\Domain\CookieSuggestion\CookieSuggestionRepositoryInterface;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;
use App\Domain\CookieSuggestion\Command\AddCookieSuggestionOccurrencesCommand;

final class AddCookieSuggestionOccurrencesCommandHandler implements CommandHandlerInterface
{
	private CookieSuggestionRepositoryInterface $cookieSuggestionRepository;

	public function __construct(
		CookieSuggestionRepositoryInterface $cookieSuggestionRepository
	) {
		$this->cookieSuggestionRepository = $cookieSuggestionRepository;
	}

	/**
	 * @throws Exception
	 */
	public function __invoke(AddCookieSuggestionOccurrencesCommand $command): void
	{
		if (empty($command->occurrences())) {
			return;
		}

		$cookieSuggestion = $this->cookieSuggestionRepository->get(CookieSuggestionId::fromString($command->cookieSuggestionId()));

		foreach ($command->occurrences() as $occurrence) {
			$cookieSuggestion->addOccurrence($occurrence);
		}

		$this->cookieSuggestionRepository->save($cookieSuggestion);
	}
}
