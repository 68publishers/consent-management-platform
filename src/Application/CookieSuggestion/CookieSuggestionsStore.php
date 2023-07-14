<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion;

use Closure;
use Exception;
use Throwable;
use DateTimeZone;
use DateTimeImmutable;
use DateTimeInterface;
use Psr\Log\LoggerInterface;
use App\ReadModel\Cookie\CookieDataForSuggestion;
use App\ReadModel\CookieSuggestion\CookieSuggestion;
use App\Application\CookieSuggestion\Solution\Solutions;
use App\Application\CookieSuggestion\Solution\DoNotIgnore;
use App\ReadModel\Cookie\FindCookieDataForSuggestionQuery;
use App\Application\CookieSuggestion\Solution\SolutionGroup;
use App\Application\CookieSuggestion\Solution\CreateNewCookie;
use App\Application\CookieSuggestion\Suggestion\ExistingCookie;
use App\Application\CookieSuggestion\Solution\IgnorePermanently;
use App\ReadModel\CookieSuggestion\CookieOccurrenceForResolving;
use App\ReadModel\CookieSuggestion\CookieSuggestionForResolving;
use App\Application\CookieSuggestion\Problem\CookieWasNeverFound;
use App\Application\CookieSuggestion\Suggestion\CookieOccurrence;
use App\Application\CookieSuggestion\DataStore\DataStoreInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\Application\CookieSuggestion\Solution\ChangeCookieCategory;
use App\Application\CookieSuggestion\Problem\CookieLongTimeNotFound;
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

	private int $longTimeNotFoundInDays;

	private Closure $getNowFunction;

	public function __construct(
		CommandBusInterface $commandBus,
		QueryBusInterface $queryBus,
		DataStoreInterface $dataStore,
		?LoggerInterface $logger = NULL,
		int $longTimeNotFoundInDays = 14
	) {
		$this->commandBus = $commandBus;
		$this->queryBus = $queryBus;
		$this->dataStore = $dataStore;
		$this->logger = $logger;
		$this->longTimeNotFoundInDays = $longTimeNotFoundInDays;
		$this->getNowFunction = static fn (): DateTimeImmutable => new DateTimeImmutable('now', new DateTimeZone('UTC'));
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

				if ($cookieRow->associated && 1 === $this->matchCookie($cookieRow, $suggestedName, $suggestedDomain)) {
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

	/**
	 * @throws Exception
	 */
	public function resolveCookieSuggestions(string $projectId): SuggestionsResult
	{
		$result = new SuggestionsResult();

		$cookieSuggestionsForResolving = $this->queryBus->dispatch(FindCookieSuggestionsForResolvingQuery::create($projectId));
		$cookiesDataForSuggestion = $this->queryBus->dispatch(FindCookieDataForSuggestionQuery::create($projectId));

		$pairedSuggestionsWithCookies = $this->pairSuggestionsWithCookies(
			$cookieSuggestionsForResolving,
			$cookiesDataForSuggestion,
		);

		$pairedSuggestionsThatWasNeverFound = [];
		$processedCookieIds = [];

		foreach ($pairedSuggestionsWithCookies as $pairedSuggestion) {
			$suggestion = $pairedSuggestion['suggestion'];
			$cookies = $pairedSuggestion['cookies'];

			# there are no occurrences - the suggestion has been created manually by a user (cookie that was never found + ignore action)
			if (0 >= count($suggestion->occurrences)) {
				# is there is also no cookies then skip this suggestion - cookie has been probably removed
				if (0 >= count($cookies)) {
					continue;
				}

				# store processed cookie ids
				$processedCookieIds[] = array_map(
					static fn (CookieDataForSuggestion $cookie): string => $cookie->id,
					$cookies,
				);

				# store paired suggestion and continue to the next pair
				$pairedSuggestionsThatWasNeverFound[] = [
					'suggestion' => $suggestion,
					'cookies' => $cookies,
				];

				continue;
			}

			# missing cookie - no one cookie is matched
			if (0 >= count($cookies)) {
				$result = $result->withSuggestion(
					$this->suggestMissingCookie($projectId, $suggestion),
				);

				continue;
			}

			# filter associated only
			$associatedCookies = array_filter(
				$cookies,
				static fn (CookieDataForSuggestion $cookie): bool => $cookie->associated,
			);

			# there are no associated cookies
			if (0 >= count($associatedCookies)) {
				# suggest unassociated cookie
				$result = $result->withSuggestion(
					$this->suggestNonAssociatedCookie($projectId, $suggestion, array_values($cookies)),
				);

				continue;
			}

			# store processed cookie ids
			$processedCookieIds[] = array_map(
				static fn (CookieDataForSuggestion $cookie): string => $cookie->id,
				$associatedCookies,
			);

			# filter only fully matched cookies
			$sameDomainCookies = array_filter(
				$associatedCookies,
				static fn (CookieDataForSuggestion $cookie): bool => $cookie->getMetadataField($cookie::METADATA_FIELD_SAME_DOMAIN) ?? FALSE,
			);

			# process only fully matched cookies if there is almost one. otherwise process not fully matched (cookies that matches name only)
			$associatedCookies = 0 < count($sameDomainCookies) ? $sameDomainCookies : $associatedCookies;

			$result = $result->withSuggestion(
				$this->suggestAssociatedCookie($projectId, $suggestion, array_values($associatedCookies)),
			);
		}

		$processedCookieIds = array_merge(...$processedCookieIds);
		$unprocessedCookies = array_filter(
			$cookiesDataForSuggestion,
			static fn (CookieDataForSuggestion $cookie): bool => $cookie->associated && !in_array($cookie->id, $processedCookieIds),
		);

		foreach ($pairedSuggestionsThatWasNeverFound as $pairedSuggestion) {
			$result = $result->withSuggestion(
				$this->suggestCookieThatWasNeverFound($projectId, $pairedSuggestion['suggestion'], array_values($pairedSuggestion['cookies']), FALSE),
			);
		}

		foreach ($this->pairUnprocessedCookies($unprocessedCookies) as $pairedSuggestion) {
			$result = $result->withSuggestion(
				$this->suggestCookieThatWasNeverFound($projectId, $pairedSuggestion['suggestion'], array_values($pairedSuggestion['cookies']), TRUE),
			);
		}

		return $result;
	}

	public function getDataStore(): DataStoreInterface
	{
		return $this->dataStore;
	}

	/**
	 * @param array<int, CookieSuggestionForResolving> $cookieSuggestionsForResolving
	 * @param array<int, CookieDataForSuggestion>      $cookiesDataForSuggestion
	 *
	 * @return array<int, array{
	 *     suggestion: CookieSuggestionForResolving,
	 *     cookies: array<string, CookieDataForSuggestion>,
	 * }>
	 */
	private function pairSuggestionsWithCookies(array $cookieSuggestionsForResolving, array $cookiesDataForSuggestion): array
	{
		$pairedSuggestions = [];

		usort(
			$cookieSuggestionsForResolving,
			static fn (CookieSuggestionForResolving $left, CookieSuggestionForResolving $right): int =>
			[$left->createdAt, $left->id] <=> [$right->createdAt, $right->id]
		);

		foreach ($cookieSuggestionsForResolving as $cookieSuggestionForResolving) {
			assert($cookieSuggestionForResolving instanceof CookieSuggestionForResolving);

			$paired = FALSE;

			foreach ($cookiesDataForSuggestion as $cookieRow) {
				assert($cookieRow instanceof CookieDataForSuggestion);

				$matchType = $this->matchCookie($cookieRow, $cookieSuggestionForResolving->name, $cookieSuggestionForResolving->domain);

				if (0 === $matchType) {
					continue;
				}

				$suggestion = $cookieSuggestionForResolving->withName($cookieRow->name);
				$key = $suggestion->domain . '__x__' . $suggestion->name;
				$paired = TRUE;

				if (!isset($pairedSuggestions[$key])) {
					$pairedSuggestions[$key] = [
						'suggestion' => $suggestion,
						'cookies' => [],
					];
				} else {
					$pairedSuggestions[$key]['suggestion'] = $pairedSuggestions[$key]['suggestion']->mergeOccurrences($suggestion->occurrences);
				}

				$pairedSuggestions[$key]['cookies'][$cookieRow->id] = $cookieRow->withMetadataField($cookieRow::METADATA_FIELD_SAME_DOMAIN, 1 === $matchType);
			}

			if (!$paired) {
				$pairedSuggestions[] = [
					'suggestion' => $cookieSuggestionForResolving,
					'cookies' => [],
				];
			}
		}

		return array_values($pairedSuggestions);
	}

	/**
	 * @param array<int, CookieDataForSuggestion> $cookiesDataForSuggestion
	 *
	 * @return array<int, array{
	 *     suggestion: CookieSuggestionForResolving,
	 *     cookies: non-empty-array<string, CookieDataForSuggestion>,
	 * }>
	 */
	private function pairUnprocessedCookies(array $cookiesDataForSuggestion): array
	{
		$cookiesBySameNameAndDomain = [];
		$pairs = [];

		foreach ($cookiesDataForSuggestion as $cookie) {
			$key = $cookie->name . '__x__' . ($cookie->domain ?: $cookie->projectDomain);

			if (!isset($cookiesBySameNameAndDomain[$key])) {
				$cookiesBySameNameAndDomain[$key] = [];
			}

			$cookiesBySameNameAndDomain[$key][] = $cookie->withMetadataField($cookie::METADATA_FIELD_SAME_DOMAIN, TRUE);
		}

		foreach ($cookiesBySameNameAndDomain as $cookies) {
			usort(
				$cookies,
				static fn (CookieDataForSuggestion $left, CookieDataForSuggestion $right): int => $left->id <=> $right->id,
			);

			$firstCookie = reset($cookies);
			assert($firstCookie instanceof CookieDataForSuggestion);

			$pairs[] = [
				'suggestion' => new CookieSuggestionForResolving(
					$firstCookie->id,
					$firstCookie->name,
					$firstCookie->domain ?: $firstCookie->projectDomain,
					$this->getNow(),
					FALSE,
					FALSE,
					[],
				),
				'cookies' => $cookies,
			];
		}

		return $pairs;
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
			new Solutions(
				$projectId,
				$cookieSuggestionForResolving->id,
				[
					'MissingCookieSuggestion',
				],
				$this->dataStore,
				new CreateNewCookie(),
				new IgnoreUntilNexOccurrence(),
				new IgnorePermanently(),
			),
		);

		if ($cookieSuggestionForResolving->isIgnored()) {
			$suggestion = new IgnoredCookieSuggestion(
				$suggestion,
				$cookieSuggestionForResolving->ignoredPermanently,
				new Solutions(
					$projectId,
					$cookieSuggestionForResolving->id,
					[
						'IgnoredCookieSuggestion',
					],
					$this->dataStore,
					new DoNotIgnore(),
				),
			);
		}

		return $suggestion;
	}

	/**
	 * @param non-empty-list<CookieDataForSuggestion> $cookieDataForSuggestionItems
	 */
	private function suggestNonAssociatedCookie(
		string $projectId,
		CookieSuggestionForResolving $cookieSuggestionForResolving,
		array $cookieDataForSuggestionItems
	): SuggestionInterface {
		$occurrences = array_map(
			fn (CookieOccurrenceForResolving $cookieOccurrenceForResolving): CookieOccurrence => $this->createCookieOccurrence($cookieOccurrenceForResolving),
			$cookieSuggestionForResolving->occurrences
		);

		$associateProviderSolutions = [];
		foreach ($cookieDataForSuggestionItems as $cookieDataForSuggestionItem) {
			$associateProviderSolutions[$cookieDataForSuggestionItem->providerId] = new AssociateCookieProviderWithProject($cookieDataForSuggestionItem->providerId, $cookieDataForSuggestionItem->providerName);
		}

		$suggestion = new UnassociatedCookieSuggestion(
			$cookieSuggestionForResolving->id,
			$cookieSuggestionForResolving->name,
			$cookieSuggestionForResolving->domain,
			$occurrences,
			array_map(
				fn (CookieDataForSuggestion $cookieDataForSuggestionItem): ExistingCookie => $this->createExistingCookie($cookieDataForSuggestionItem),
				$cookieDataForSuggestionItems,
			),
			new Solutions(
				$projectId,
				$cookieSuggestionForResolving->id,
				[
					'UnassociatedCookieSuggestion',
				],
				$this->dataStore,
				new SolutionGroup(
					'associate_cookie_provider_with_project',
					...array_values($associateProviderSolutions),
				),
				new CreateNewCookie(),
				new IgnoreUntilNexOccurrence(),
				new IgnorePermanently(),
			),
		);

		if ($cookieSuggestionForResolving->isIgnored()) {
			$suggestion = new IgnoredCookieSuggestion(
				$suggestion,
				$cookieSuggestionForResolving->ignoredPermanently,
				new Solutions(
					$projectId,
					$cookieSuggestionForResolving->id,
					[
						'IgnoredCookieSuggestion',
					],
					$this->dataStore,
					new DoNotIgnore(),
				),
			);
		}

		return $suggestion;
	}

	/**
	 * @param non-empty-list<CookieDataForSuggestion> $cookieDataForSuggestionItems
	 *
	 * @throws Exception
	 */
	private function suggestAssociatedCookie(
		string $projectId,
		CookieSuggestionForResolving $cookieSuggestionForResolving,
		array $cookieDataForSuggestionItems
	): SuggestionInterface {
		$occurrences = [];
		$problems = [];
		$lastFoundAt = NULL;
		$existingCookiesCategories = array_unique(
			array_map(
				fn (CookieDataForSuggestion $cookieDataForSuggestionItem): string => $cookieDataForSuggestionItem->categoryCode,
				$cookieDataForSuggestionItems,
			)
		);

		foreach ($cookieSuggestionForResolving->occurrences as $occurrence) {
			$occurrences[] = $cookieOccurrence = $this->createCookieOccurrence($occurrence);
			$categoriesIntersect = array_intersect($existingCookiesCategories, $cookieOccurrence->acceptedCategories);

			if (NULL === $lastFoundAt || $lastFoundAt < $occurrence->lastFoundAt) {
				$lastFoundAt = $occurrence->lastFoundAt;
			}

			if (0 >= count($categoriesIntersect)) {
				$acceptedCategories = $cookieOccurrence->acceptedCategories;
				sort($acceptedCategories);

				$problemKey = sprintf(
					'CookieIsInCategoryThatIsNotAcceptedByScenario__%s__%s',
					implode(';', $categoriesIntersect),
					implode(';', $acceptedCategories),
				);

				$problems[$problemKey] = new CookieIsInCategoryThatIsNotAcceptedByScenario(
					$existingCookiesCategories,
					$cookieOccurrence->acceptedCategories,
					$cookieOccurrence,
					new Solutions(
						$projectId,
						$cookieSuggestionForResolving->id,
						[
							$cookieOccurrence->id,
							'ProblematicCookieSuggestion',
							CookieIsInCategoryThatIsNotAcceptedByScenario::TYPE,
						],
						$this->dataStore,
						new SolutionGroup(
							'change_cookie_category',
							...array_map(
								fn (CookieDataForSuggestion $cookieDataForSuggestionItem): ChangeCookieCategory => new ChangeCookieCategory(
									$cookieDataForSuggestionItem->id,
									$cookieDataForSuggestionItem->categoryCode,
									$cookieDataForSuggestionItem->providerName,
								),
								$cookieDataForSuggestionItems,
							),
						),
						new SolutionGroup(
							'create_new_cookie_with_not_accepted_category',
							...array_map(
								fn (CookieDataForSuggestion $cookieDataForSuggestionItem): CreateNewCookieWithNotAcceptedCategory => new CreateNewCookieWithNotAcceptedCategory(
									$cookieDataForSuggestionItem->id,
									$cookieDataForSuggestionItem->categoryCode,
									$cookieDataForSuggestionItem->providerName,
								),
								$cookieDataForSuggestionItems,
							),
						),
						new IgnoreUntilNexOccurrence(),
						new IgnorePermanently(),
					),
				);
			}
		}

		$now = $this->getNow();
		$comparisonDateTime = $now->modify(sprintf('-%d days', $this->longTimeNotFoundInDays));

		if (NULL === $lastFoundAt) {
			$problems[] = new CookieWasNeverFound(
				new Solutions(
					$projectId,
					$cookieSuggestionForResolving->id,
					[
						'ProblematicCookieSuggestion',
						CookieWasNeverFound::TYPE,
					],
					$this->dataStore,
					new IgnoreUntilNexOccurrence(),
					new IgnorePermanently(),
				)
			);
		} elseif ($lastFoundAt < $comparisonDateTime) {
			$notFoundForDays = $now
				->diff($lastFoundAt)
				->days;

			$problems[] = new CookieLongTimeNotFound(
				$notFoundForDays,
				new Solutions(
					$projectId,
					$cookieSuggestionForResolving->id,
					[
						'ProblematicCookieSuggestion',
						CookieLongTimeNotFound::TYPE,
					],
					$this->dataStore,
					new IgnoreUntilNexOccurrence(),
					new IgnorePermanently(),
				)
			);
		}

		$existingCookies = array_map(
			fn (CookieDataForSuggestion $cookieDataForSuggestionItem): ExistingCookie => $this->createExistingCookie($cookieDataForSuggestionItem),
			$cookieDataForSuggestionItems,
		);

		$suggestion = 0 < count($problems)
			? new ProblematicCookieSuggestion(
				$cookieSuggestionForResolving->id,
				$cookieSuggestionForResolving->name,
				$cookieSuggestionForResolving->domain,
				$occurrences,
				$existingCookies,
				array_values($problems),
			)
			: new UnproblematicCookieSuggestion(
				$cookieSuggestionForResolving->id,
				$cookieSuggestionForResolving->name,
				$cookieSuggestionForResolving->domain,
				$occurrences,
				$existingCookies,
			);

		if ($cookieSuggestionForResolving->isIgnored() && $suggestion instanceof ProblematicCookieSuggestion) {
			$suggestion = new IgnoredCookieSuggestion(
				$suggestion,
				$cookieSuggestionForResolving->ignoredPermanently,
				new Solutions(
					$projectId,
					$cookieSuggestionForResolving->id,
					[
						'IgnoredCookieSuggestion',
					],
					$this->dataStore,
					new DoNotIgnore(),
				),
			);
		}

		return $suggestion;
	}

	/**
	 * @param non-empty-list<CookieDataForSuggestion> $cookieDataForSuggestionItems
	 *
	 * @throws Exception
	 */
	private function suggestCookieThatWasNeverFound(
		string $projectId,
		CookieSuggestionForResolving $cookieSuggestionForResolving,
		array $cookieDataForSuggestionItems,
		bool $virtual
	): SuggestionInterface {
		$suggestion = new ProblematicCookieSuggestion(
			$cookieSuggestionForResolving->id,
			$cookieSuggestionForResolving->name,
			$cookieSuggestionForResolving->domain,
			[],
			array_map(
				fn (CookieDataForSuggestion $cookieDataForSuggestionItem): ExistingCookie => $this->createExistingCookie($cookieDataForSuggestionItem),
				$cookieDataForSuggestionItems,
			),
			[
				new CookieWasNeverFound(
					new Solutions(
						$projectId,
						$cookieSuggestionForResolving->id,
						[
							'ProblematicCookieSuggestion',
							CookieWasNeverFound::TYPE,
						],
						$this->dataStore,
						new IgnoreUntilNexOccurrence($virtual),
						new IgnorePermanently($virtual),
					)
				),
			],
			$virtual,
		);

		if ($cookieSuggestionForResolving->isIgnored()) {
			$suggestion = new IgnoredCookieSuggestion(
				$suggestion,
				$cookieSuggestionForResolving->ignoredPermanently,
				new Solutions(
					$projectId,
					$cookieSuggestionForResolving->id,
					[
						'IgnoredCookieSuggestion',
					],
					$this->dataStore,
					new DoNotIgnore(),
				),
			);
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

	private function matchCookie(CookieDataForSuggestion $cookieDataForSuggestion, string $suggestedCookieName, string $suggestedCookieDomain): int
	{
		return Matcher::matchCookie(
			$cookieDataForSuggestion->name,
			$cookieDataForSuggestion->domain,
			$cookieDataForSuggestion->projectDomain,
			$suggestedCookieName,
			$suggestedCookieDomain,
		);
	}

	private function getNow(): DateTimeImmutable
	{
		$now = ($this->getNowFunction)();
		assert($now instanceof DateTimeImmutable);

		return $now;
	}
}
