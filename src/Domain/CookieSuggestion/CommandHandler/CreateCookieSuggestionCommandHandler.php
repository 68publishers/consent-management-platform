<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion\CommandHandler;

use Exception;
use App\Domain\CookieSuggestion\CookieSuggestion;
use App\Domain\CookieSuggestion\CookieSuggestionRepositoryInterface;
use App\Domain\CookieSuggestion\Command\CreateCookieSuggestionCommand;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;
use App\Domain\CookieSuggestion\CheckSuggestionNameAndDomainUniquenessInterface;

final class CreateCookieSuggestionCommandHandler implements CommandHandlerInterface
{
	private CookieSuggestionRepositoryInterface $cookieSuggestionRepository;

	private CheckSuggestionNameAndDomainUniquenessInterface $checkSuggestionNameAndDomainUniqueness;

	public function __construct(
		CookieSuggestionRepositoryInterface $cookieSuggestionRepository,
		CheckSuggestionNameAndDomainUniquenessInterface $checkSuggestionNameAndDomainUniqueness
	) {
		$this->cookieSuggestionRepository = $cookieSuggestionRepository;
		$this->checkSuggestionNameAndDomainUniqueness = $checkSuggestionNameAndDomainUniqueness;
	}

	/**
	 * @throws Exception
	 */
	public function __invoke(CreateCookieSuggestionCommand $command): void
	{
		$cookieSuggestion = CookieSuggestion::create($command, $this->checkSuggestionNameAndDomainUniqueness);

		$this->cookieSuggestionRepository->save($cookieSuggestion);
	}
}
