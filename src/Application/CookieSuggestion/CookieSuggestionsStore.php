<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion;

use Throwable;
use DateTimeImmutable;
use DateTimeInterface;
use Psr\Log\LoggerInterface;
use App\ReadModel\Cookie\CookieDataForSuggestion;
use App\ReadModel\CookieSuggestion\CookieSuggestion;
use App\Application\CookieSuggestion\Solution\Solutions;
use App\ReadModel\Cookie\FindCookieDataForSuggestionQuery;
use App\Application\CookieSuggestion\Solution\CreateNewCookie;
use App\Application\CookieSuggestion\Suggestion\ExistingCookie;
use App\Application\CookieSuggestion\Warning\CookieDomainNotSet;
use App\ReadModel\CookieSuggestion\CookieOccurrenceForResolving;
use App\ReadModel\CookieSuggestion\CookieSuggestionForResolving;
use App\Application\CookieSuggestion\Suggestion\CookieOccurrence;
use App\Application\CookieSuggestion\DataStore\DataStoreInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\Application\CookieSuggestion\Solution\ChangeCookieCategory;
use App\Application\CookieSuggestion\Suggestion\SuggestionInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use App\Domain\CookieSuggestion\Command\CreateCookieSuggestionCommand;
use App\Application\CookieSuggestion\Solution\IgnoreUntilNexOccurrence;
use App\Application\CookieSuggestion\Suggestion\IgnoredCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\MissingCookieSuggestion;
use App\ReadModel\CookieSuggestion\FindCookieSuggestionsForResolvingQuery;
use App\Application\CookieSuggestion\Suggestion\ProblematicCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\UnassociatedCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\UnproblematicCookieSuggestion;
use App\Domain\CookieSuggestion\Command\AddCookieSuggestionOccurrencesCommand;
use App\Application\CookieSuggestion\Solution\AssociateCookieProviderWithProject;
use App\Domain\CookieSuggestion\Command\CookieOccurrence as CommandCookieOccurrence;
use App\Application\CookieSuggestion\Solution\CreateNewCookieWithNotAcceptedCategory;
use App\ReadModel\CookieSuggestion\GetCookieSuggestionByProjectIdAndNameAndDomainQuery;
use App\Application\CookieSuggestion\Problem\CookieIsInCategoryThatIsNotAcceptedByScenario;

final class CookieSuggestionsStore implements CookieSuggestionsStoreInterface
{
	private CommandBusInterface $commandBus;

	private QueryBusInterface $queryBus;

	private DataStoreInterface $dataStore;

	private ?LoggerInterface $logger;

	public function __construct(
		CommandBusInterface $commandBus,
		QueryBusInterface $queryBus,
		DataStoreInterface $dataStore,
		?LoggerInterface $logger = NULL
	) {
		$this->commandBus = $commandBus;
		$this->queryBus = $queryBus;
		$this->dataStore = $dataStore;
		$this->logger = $logger;
	}

	/**
	 * @throws Throwable
	 */
	public function storeCrawledCookies(string $scenarioName, string $projectId, array $acceptedCategories, DateTimeImmutable $finishedAt, array $cookies): void
	{
		$cookiesData = NULL;
		$getCookiesData = function () use (&$cookiesData, $projectId): array {
			if (NULL !== $cookiesData) {
				return $cookiesData;
			}

			return $cookiesData = $this->queryBus->dispatch(FindCookieDataForSuggestionQuery::create($projectId));
		};

		$resolveCookieName = function ($suggestedName, $suggestedDomain) use ($getCookiesData): string {
			foreach ($getCookiesData() as $cookieRow) {
				assert($cookieRow instanceof CookieDataForSuggestion);

				if ($cookieRow->associated && 1 === $this->matchCookie($cookieRow->name, $cookieRow->domain, $suggestedName, $suggestedDomain)) {
					return $cookieRow->name;
				}
			}

			return $suggestedName;
		};

		foreach ($cookies as $cookie) {
			try {
				$cookieName = $resolveCookieName($cookie->name, $cookie->domain);

				$existingCookieSuggestion = $this->queryBus->dispatch(GetCookieSuggestionByProjectIdAndNameAndDomainQuery::create(
					$projectId,
					$cookieName,
					$cookie->domain,
				));

				$occurrence = new CommandCookieOccurrence(
					$scenarioName,
					$cookie->foundOnUrl,
					$acceptedCategories,
					$finishedAt->format(DateTimeInterface::ATOM)
				);

				$command = $existingCookieSuggestion instanceof CookieSuggestion
					? AddCookieSuggestionOccurrencesCommand::create(
						$existingCookieSuggestion->id,
						[$occurrence],
					)
					: CreateCookieSuggestionCommand::create(
						$projectId,
						$cookieName,
						$cookie->domain,
						[$occurrence],
					);

				$this->commandBus->dispatch($command);
			} catch (Throwable $e) {
				if (NULL === $this->logger) {
					throw $e;
				}

				$this->logger->error(sprintf(
					"Error during storing crawled cookie %s for project %s.\n%s",
					@json_encode($cookie),
					$projectId,
					$e,
				));
			}
		}
	}

	public function resolveCookieSuggestions(string $projectId): SuggestionsResult
	{
		$result = new SuggestionsResult();
		$pairedSuggestionsWithCookies = $this->pairSuggestionsWithCookies($projectId);

		foreach ($pairedSuggestionsWithCookies as $pairedSuggestion) {
			$suggestion = $pairedSuggestion['suggestion'];
			$matchedCookies = $pairedSuggestion['matched'];
			$matchedCookiesWithoutDomain = $pairedSuggestion['matchedWithoutDomain'];

			if (0 >= count($matchedCookies) && 0 >= count($matchedCookiesWithoutDomain)) {
				$result = $result->withSuggestion(
					$this->suggestMissingCookie($projectId, $suggestion),
				);
			}

			foreach ($matchedCookies as $matchedCookie) {
				if ($matchedCookie->associated) {
					$result = $result->withSuggestion(
						$this->suggestAssociatedCookie($projectId, $suggestion, $matchedCookie, FALSE),
					);
				} else {
					$result = $result->withSuggestion(
						$this->suggestNonAssociatedCookie($projectId, $suggestion, $matchedCookie, FALSE),
					);
				}
			}

			foreach ($matchedCookiesWithoutDomain as $matchedCookieWithoutDomain) {
				if ($matchedCookieWithoutDomain->associated) {
					$result = $result->withSuggestion(
						$this->suggestAssociatedCookie($projectId, $suggestion, $matchedCookieWithoutDomain, TRUE),
					);
				} else {
					$result = $result->withSuggestion(
						$this->suggestNonAssociatedCookie($projectId, $suggestion, $matchedCookieWithoutDomain, TRUE),
					);
				}
			}
		}

		return $result;
	}

	public function getDataStore(): DataStoreInterface
	{
		return $this->dataStore;
	}

	/**
	 * @return array<int, array{
	 *     suggestion: CookieSuggestionForResolving,
	 *     matched: array<int, CookieDataForSuggestion>,
	 *     matchedWithoutDomain: array<int, CookieDataForSuggestion>,
	 * }>
	 */
	private function pairSuggestionsWithCookies(string $projectId): array
	{
		$cookieSuggestionsForResolving = $this->queryBus->dispatch(FindCookieSuggestionsForResolvingQuery::create($projectId));
		$cookiesDataForSuggestion = $this->queryBus->dispatch(FindCookieDataForSuggestionQuery::create($projectId));

		$pairedSuggestions = [];

		foreach ($cookieSuggestionsForResolving as $cookieSuggestionForResolving) {
			assert($cookieSuggestionForResolving instanceof CookieSuggestionForResolving);

			$matched = [];
			$matchedWithoutDomain = [];

			foreach ($cookiesDataForSuggestion as $cookieRow) {
				assert($cookieRow instanceof CookieDataForSuggestion);

				$matchType = $this->matchCookie($cookieRow->name, $cookieRow->domain, $cookieSuggestionForResolving->name, $cookieSuggestionForResolving->domain);

				if (1 === $matchType) {
					$matched[] = $cookieRow;
				} elseif (2 === $matchType) {
					$matchedWithoutDomain[] = $cookieRow;
				}
			}

			$pairedSuggestions[] = [
				'suggestion' => $cookieSuggestionForResolving,
				'matched' => $matched,
				'matchedWithoutDomain' => $matchedWithoutDomain,
			];
		}

		return $pairedSuggestions;
	}

	private function suggestMissingCookie(string $projectId, CookieSuggestionForResolving $cookieSuggestionForResolving): SuggestionInterface
	{
		$occurrences = array_map(
			fn (CookieOccurrenceForResolving $cookieOccurrenceForResolving): CookieOccurrence => $this->createCookieOccurrence($cookieOccurrenceForResolving),
			$cookieSuggestionForResolving->occurrences
		);

		$suggestion = new MissingCookieSuggestion(
			$cookieSuggestionForResolving->id,
			$cookieSuggestionForResolving->name,
			$cookieSuggestionForResolving->domain,
			$occurrences,
			[],
			new Solutions(
				[
					$projectId,
					$cookieSuggestionForResolving->id,
					'MissingCookieSuggestion',
				],
				$this->dataStore,
				new CreateNewCookie(),
				new IgnoreUntilNexOccurrence(),
			),
		);

		if ($cookieSuggestionForResolving->ignored) {
			$suggestion = new IgnoredCookieSuggestion($suggestion);
		}

		return $suggestion;
	}

	private function suggestNonAssociatedCookie(
		string $projectId,
		CookieSuggestionForResolving $cookieSuggestionForResolving,
		CookieDataForSuggestion $cookieDataForSuggestion,
		bool $matchedWithoutDomain
	): SuggestionInterface {
		$occurrences = array_map(
			fn (CookieOccurrenceForResolving $cookieOccurrenceForResolving): CookieOccurrence => $this->createCookieOccurrence($cookieOccurrenceForResolving),
			$cookieSuggestionForResolving->occurrences
		);

		$warnings = !$matchedWithoutDomain ? [] : [
			new CookieDomainNotSet(),
		];

		$suggestion = new UnassociatedCookieSuggestion(
			$cookieSuggestionForResolving->id,
			$cookieSuggestionForResolving->name,
			$cookieSuggestionForResolving->domain,
			$occurrences,
			$warnings,
			$this->createExistingCookie($cookieDataForSuggestion),
			new Solutions(
				[
					$projectId,
					$cookieSuggestionForResolving->id,
					$cookieDataForSuggestion->id,
					'UnassociatedCookieSuggestion',
				],
				$this->dataStore,
				new AssociateCookieProviderWithProject($cookieDataForSuggestion->providerId),
				new CreateNewCookie(),
				new IgnoreUntilNexOccurrence(),
			),
		);

		if ($cookieSuggestionForResolving->ignored) {
			$suggestion = new IgnoredCookieSuggestion($suggestion);
		}

		return $suggestion;
	}

	private function suggestAssociatedCookie(
		string $projectId,
		CookieSuggestionForResolving $cookieSuggestionForResolving,
		CookieDataForSuggestion $cookieDataForSuggestion,
		bool $matchedWithoutDomain
	): SuggestionInterface {
		$occurrences = [];
		$problems = [];

		foreach ($cookieSuggestionForResolving->occurrences as $occurrence) {
			$occurrences[] = $cookieOccurrence = $this->createCookieOccurrence($occurrence);

			if (!in_array($cookieDataForSuggestion->categoryCode, $cookieOccurrence->acceptedCategories)) {
				$problems[] = new CookieIsInCategoryThatIsNotAcceptedByScenario(
					$cookieDataForSuggestion->categoryCode,
					$cookieOccurrence->acceptedCategories,
					$cookieOccurrence,
					new Solutions(
						[
							$projectId,
							$cookieSuggestionForResolving->id,
							$cookieDataForSuggestion->id,
							$cookieOccurrence->id,
							'ProblematicCookieSuggestion',
							CookieIsInCategoryThatIsNotAcceptedByScenario::TYPE,
						],
						$this->dataStore,
						new ChangeCookieCategory($cookieDataForSuggestion->id),
						new CreateNewCookieWithNotAcceptedCategory($cookieDataForSuggestion->id),
						new IgnoreUntilNexOccurrence(),
					),
				);
			}
		}

		$warnings = !$matchedWithoutDomain ? [] : [
			new CookieDomainNotSet(),
		];
		$existingCookie = $this->createExistingCookie($cookieDataForSuggestion);

		$suggestion = 0 < count($problems)
			? new ProblematicCookieSuggestion(
				$cookieSuggestionForResolving->id,
				$cookieSuggestionForResolving->name,
				$cookieSuggestionForResolving->domain,
				$occurrences,
				$warnings,
				$existingCookie,
				$problems,
			)
			: new UnproblematicCookieSuggestion(
				$cookieSuggestionForResolving->id,
				$cookieSuggestionForResolving->name,
				$cookieSuggestionForResolving->domain,
				$occurrences,
				$warnings,
				$existingCookie,
			);

		if ($cookieSuggestionForResolving->ignored && $suggestion instanceof ProblematicCookieSuggestion) {
			$suggestion = new IgnoredCookieSuggestion($suggestion);
		}

		return $suggestion;
	}

	private function createExistingCookie(CookieDataForSuggestion $cookieDataForSuggestion): ExistingCookie
	{
		return ExistingCookie::fromCookieDataForSuggestion($cookieDataForSuggestion);
	}

	private function createCookieOccurrence(CookieOccurrenceForResolving $cookieOccurrenceForResolving): CookieOccurrence
	{
		return CookieOccurrence::fromCookieOccurrenceForResolving($cookieOccurrenceForResolving);
	}

	/**
	 * 0 - no match
	 * 1 - matched (both name and domain)
	 * 2 - similar (matched name and cookie domain is empty)
	 */
	private function matchCookie(string $cookieName, string $cookieDomain, string $suggestedCookieName, string $suggestedCookieDomain): int
	{
		$successfulResult = 2;

		if (!empty($cookieDomain)) {
			$cookieDomain = 0 === strncmp($cookieDomain, '.', 1) ? substr($cookieDomain, 1) : $cookieDomain;
			$suggestedCookieDomain = 0 === strncmp($suggestedCookieDomain, '.', 1) ? substr($suggestedCookieDomain, 1) : $suggestedCookieDomain;

			if ($cookieDomain !== $suggestedCookieDomain) {
				return 0;
			}

			$successfulResult = 1;
		}

		if (FALSE === strpos($cookieName, '*')) {
			return $cookieName === $suggestedCookieName ? $successfulResult : 0;
		}

		$regex = str_replace(
			["\*"], # wildcard chars
			['.*'], # regexp chars
			preg_quote($cookieName, '/')
		);

		return preg_match('/^'.$regex.'$/s', $suggestedCookieName) ? $successfulResult : 0;
	}
}
