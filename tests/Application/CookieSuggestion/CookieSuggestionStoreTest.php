<?php

declare(strict_types=1);

namespace App\Tests\Application\CookieSuggestion;

use Closure;
use Mockery;
use DateTimeZone;
use Tester\Assert;
use Tester\TestCase;
use DateTimeImmutable;
use DateTimeInterface;
use Hamcrest\Core\IsEqual;
use App\ReadModel\Cookie\CookieDataForSuggestion;
use App\ReadModel\CookieSuggestion\CookieSuggestion;
use App\Application\CookieSuggestion\Solution\Solutions;
use App\Application\CookieSuggestion\Solution\DoNotIgnore;
use App\ReadModel\Cookie\FindCookieDataForSuggestionQuery;
use App\Application\CookieSuggestion\CookieSuggestionsStore;
use App\Application\CookieSuggestion\Solution\SolutionGroup;
use App\Application\CookieSuggestion\Solution\CreateNewCookie;
use App\Application\CookieSuggestion\DataStore\MemoryDataStore;
use App\Application\CookieSuggestion\Suggestion\ExistingCookie;
use App\Application\CookieSuggestion\Solution\IgnorePermanently;
use App\ReadModel\CookieSuggestion\CookieOccurrenceForResolving;
use App\ReadModel\CookieSuggestion\CookieSuggestionForResolving;
use App\Application\CookieSuggestion\Problem\CookieWasNeverFound;
use App\Application\CookieSuggestion\Suggestion\CookieOccurrence;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\Application\CookieSuggestion\Solution\ChangeCookieCategory;
use App\Application\CookieSuggestion\Problem\CookieLongTimeNotFound;
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
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ValueObject\Cookie as CrawlerClientCookie;

require __DIR__ . '/../../bootstrap.php';

final class CookieSuggestionStoreTest extends TestCase
{
	public function testCrawledCookieStoring(): void
	{
		$scenarioName = 'test';
		$projectId = '1786dd53-4493-4e52-aefe-059863757c70';
		$acceptedCategories = [
			'category1',
			'category2',
		];
		$finishedAt = new DateTimeImmutable('2023-06-24 12:00:00');
		$finishedAtString = $finishedAt->format(DateTimeInterface::ATOM);
		$store = $this->createStore(
			[
				[
					FindCookieDataForSuggestionQuery::create($projectId),
					[
						new CookieDataForSuggestion('912b294d-cd6e-45f8-a05a-5f8c79859986', 'ExistingCookie', '', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'test_category', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'test_provider', 'Test provider', TRUE),
						new CookieDataForSuggestion('3bd7dbaf-7fb5-4867-89de-8d108497f935', 'AssociatedWildcardCookie*', '', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'test_category', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'test_provider', 'Test provider', TRUE),
						new CookieDataForSuggestion('f85b68cd-0fc9-4ca6-9517-a63bc01f5749', 'NonAssociatedWildcardCookie*', '', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'test_category', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'test_provider', 'Test provider', FALSE),
						new CookieDataForSuggestion('85ad37ea-b319-4e30-b754-e87f567b0455', 'WildcardCookieWithDifferentDomain_*', 'google.com', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'test_category', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'test_provider', 'Test provider', TRUE),
						new CookieDataForSuggestion('91aa5ec3-220e-4bd3-b793-a64697300b9a', 'ExistingCookieWithEmptyDomain', '', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'test_category', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'test_provider', 'Test provider', TRUE),
						new CookieDataForSuggestion('4dff0d51-c23f-4670-b617-540f73beb410', 'WildcardCookieWithEmptyDomain_*', '', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'test_category', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'test_provider', 'Test provider', TRUE),
					],
				],
				[
					GetCookieSuggestionByProjectIdAndNameAndDomainQuery::create($projectId, 'ExistingCookie', 'example.com'),
					new CookieSuggestion('f17fdace-f261-4cda-9524-ab4b7e6f1968', $projectId, 'ExistingCookie', 'example.com', new DateTimeImmutable('2023-06-27 12:00:00')),
				],
				[
					GetCookieSuggestionByProjectIdAndNameAndDomainQuery::create($projectId, 'MissingCookie', 'example.com'),
					NULL,
				],
				[
					GetCookieSuggestionByProjectIdAndNameAndDomainQuery::create($projectId, 'AssociatedWildcardCookie*', 'example.com'),
					new CookieSuggestion('13e2446e-d61b-430e-8085-c96f99e82b41', $projectId, 'AssociatedWildcardCookie*', 'example.com', new DateTimeImmutable('2023-06-27 13:00:00')),
				],
				[
					GetCookieSuggestionByProjectIdAndNameAndDomainQuery::create($projectId, 'NonAssociatedWildcardCookie987654', 'example.com'),
					NULL,
				],
				[
					GetCookieSuggestionByProjectIdAndNameAndDomainQuery::create($projectId, 'WildcardCookieWithDifferentDomain_123456', 'example.com'),
					NULL,
				],
				[
					GetCookieSuggestionByProjectIdAndNameAndDomainQuery::create($projectId, 'ExistingCookieWithEmptyDomain', 'example.com'),
					NULL,
				],
				[
					GetCookieSuggestionByProjectIdAndNameAndDomainQuery::create($projectId, 'WildcardCookieWithEmptyDomain_*', 'example.com'),
					NULL,
				],
			],
			[
				AddCookieSuggestionOccurrencesCommand::create('f17fdace-f261-4cda-9524-ab4b7e6f1968', [
					new CommandCookieOccurrence($scenarioName, 'https://www.example.com/', $acceptedCategories, $finishedAtString),
				]),
				CreateCookieSuggestionCommand::create($projectId, 'MissingCookie', 'example.com', [
					new CommandCookieOccurrence($scenarioName, 'https://www.example.com/page1', $acceptedCategories, $finishedAtString),
				]),
				AddCookieSuggestionOccurrencesCommand::create('13e2446e-d61b-430e-8085-c96f99e82b41', [
					new CommandCookieOccurrence($scenarioName, 'https://www.example.com/page2', $acceptedCategories, $finishedAtString),
				]),
				CreateCookieSuggestionCommand::create($projectId, 'NonAssociatedWildcardCookie987654', 'example.com', [
					new CommandCookieOccurrence($scenarioName, 'https://www.example.com/page2', $acceptedCategories, $finishedAtString),
				]),
				CreateCookieSuggestionCommand::create($projectId, 'WildcardCookieWithDifferentDomain_123456', 'example.com', [
					new CommandCookieOccurrence($scenarioName, 'https://www.example.com/page3', $acceptedCategories, $finishedAtString),
				]),
				CreateCookieSuggestionCommand::create($projectId, 'ExistingCookieWithEmptyDomain', 'example.com', [
					new CommandCookieOccurrence($scenarioName, 'https://www.example.com/page4', $acceptedCategories, $finishedAtString),
				]),
				CreateCookieSuggestionCommand::create($projectId, 'WildcardCookieWithEmptyDomain_*', 'example.com', [
					new CommandCookieOccurrence($scenarioName, 'https://www.example.com/page5', $acceptedCategories, $finishedAtString),
				]),
			],
		);

		$store->storeCrawledCookies($scenarioName, $projectId, $acceptedCategories, $finishedAt, [
			$this->createCookieToStore('ExistingCookie', 'example.com', 'https://www.example.com/'),
			$this->createCookieToStore('MissingCookie', 'example.com', 'https://www.example.com/page1'),
			$this->createCookieToStore('AssociatedWildcardCookie123456', 'example.com', 'https://www.example.com/page2'),
			$this->createCookieToStore('NonAssociatedWildcardCookie987654', 'example.com', 'https://www.example.com/page2'),
			$this->createCookieToStore('WildcardCookieWithDifferentDomain_123456', 'example.com', 'https://www.example.com/page3'),
			$this->createCookieToStore('ExistingCookieWithEmptyDomain', 'example.com', 'https://www.example.com/page4'),
			$this->createCookieToStore('WildcardCookieWithEmptyDomain_98765', 'example.com', 'https://www.example.com/page5'),
		]);

		# dummy assertion, all checks are done by mockery
		Assert::true(TRUE);
	}

	public function testCookieSuggestionsResolving(): void
	{
		$projectId = '1786dd53-4493-4e52-aefe-059863757c70';
		$now = new DateTimeImmutable('2023-06-30 12:00:00', new DateTimeZone('UTC'));
		$createdAt = new DateTimeImmutable('2023-06-27 12:00:00', new DateTimeZone('UTC'));

		# ===== Missing =====
		$missingNormal = new CookieSuggestionForResolving('9189459d-f5f5-41b9-b1fd-b4a6cc914ebc', 'Missing_normal', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), FALSE, FALSE, [
			$missingNormalOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Missing_normal', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
		]);
		$missingIgnored = new CookieSuggestionForResolving('86a0ecaf-44ec-4847-b43b-562a966c7c86', 'Missing_ignored', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), TRUE, FALSE, [
			$missingIgnoredOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Missing_ignored', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
		]);

		# ===== Unproblematic =====
		$unproblematicNormal = new CookieSuggestionForResolving('17af2175-7970-436f-8ae5-d36400d17b2a', 'Unproblematic_normal', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), FALSE, FALSE, [
			$unproblematicNormalOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Unproblematic_normal', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
		]);
		$unproblematicWildcard = new CookieSuggestionForResolving('0007e99a-49a4-4c63-9eaf-eb6464cc9d04', 'Unproblematic_wildcard_12234', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), FALSE, FALSE, [
			$unproblematicWildcardOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Unproblematic_wildcard_12234', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
		]);
		$unproblematicWildcard2 = new CookieSuggestionForResolving('0007e99a-49a4-4c63-9eaf-eb6464cc9d04', 'Unproblematic_wildcard_*', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), FALSE, FALSE, [
			$unproblematicWildcard2Occurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Unproblematic_wildcard_*', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
		]);

		# ===== Unassociated =====
		$unassociatedNormal = new CookieSuggestionForResolving('9df8977b-9d44-46be-94ea-f6399cc327b1', 'Unassociated_normal', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), FALSE, FALSE, [
			$unassociatedNormalOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Unassociated_normal', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
		]);
		$unassociatedWildcard = new CookieSuggestionForResolving('0d5db7d0-b81d-4514-98e5-04a218451d17', 'Unassociated_wildcard_34556a', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), FALSE, FALSE, [
			$unassociatedWildcardOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Unassociated_wildcard_34556a', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
		]);
		$unassociatedIgnored = new CookieSuggestionForResolving('fa47cb13-90a8-4eb4-b2b1-30c0acc8b54b', 'Unassociated_ignored', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), FALSE, TRUE, [
			$unassociatedIgnoredOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Unassociated_ignored', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
		]);

		# ===== Problematic =====
		# category problems:
		$problematicSingleCategory = new CookieSuggestionForResolving('9df8977b-9d44-46be-94ea-f6399cc327b1', 'Problematic_single_category', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), FALSE, FALSE, [
			$problematicSingleCategoryOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Problematic_single_category', 'https://www.example.com/page1', ['categoryA', 'categoryB', 'categoryC'], new DateTimeImmutable('2023-06-24 12:00:00')),
			$problematicSingleCategoryOccurrence2 = new CookieOccurrenceForResolving('561109e2-5944-44ae-aedb-dcbc58e62d55', 'ScenarioB', 'Problematic_single_category', 'https://www.example.com/page2', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 14:00:00')),
		]);
		$problematicSingleCategoryIgnored = new CookieSuggestionForResolving('46ac6172-9f8e-492a-bef7-832f5055e201', 'Problematic_single_category_ignored', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), TRUE, TRUE, [
			$problematicSingleCategoryIgnoredOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Problematic_single_category_ignored', 'https://www.example.com/page1', ['categoryA', 'categoryB', 'categoryC'], new DateTimeImmutable('2023-06-24 12:00:00')),
			$problematicSingleCategoryIgnoredOccurrence2 = new CookieOccurrenceForResolving('561109e2-5944-44ae-aedb-dcbc58e62d55', 'ScenarioB', 'Problematic_single_category_ignored', 'https://www.example.com/page2', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 14:00:00')),
		]);
		$problematicTwoCategories = new CookieSuggestionForResolving('42c660ce-6770-4002-abd8-338a4015444d', 'Problematic_two_categories', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), FALSE, FALSE, [
			$problematicTwoCategoriesOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Problematic_two_categories', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
			$problematicTwoCategoriesOccurrence2 = new CookieOccurrenceForResolving('561109e2-5944-44ae-aedb-dcbc58e62d55', 'ScenarioB', 'Problematic_two_categories', 'https://www.example.com/page2', ['categoryA', 'categoryD'], new DateTimeImmutable('2023-06-24 14:00:00')),
		]);
		# long time not found problems:
		$problematicLongTimeNotFound = new CookieSuggestionForResolving('9ff5b3b4-6b70-40d7-ae7f-2122651a70b4', 'Problematic_long_time_not_found', 'example.com', $now->modify('-15 days'), FALSE, FALSE, [
			$problematicLongTimeNotFoundOccurrence1 = new CookieOccurrenceForResolving('414db989-a6f8-4766-838e-82dd8129b164', 'ScenarioA', 'Problematic_long_time_not_found', 'https://www.example.com/page1', ['categoryA', 'categoryB', 'categoryC'], $now->modify('-15 days')),
			$problematicLongTimeNotFoundOccurrence2 = new CookieOccurrenceForResolving('8b9f5fac-d0de-4145-92db-d8b83fbbb8fb', 'ScenarioB', 'Problematic_long_time_not_found', 'https://www.example.com/page2', ['categoryA', 'categoryB'], $now->modify('-18 days')),
		]);
		# never found - existing
		$problematicNeveFound = new CookieSuggestionForResolving('105da7b4-2fbd-475f-bb63-1b18342f782a', 'Problematic_never_found', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), FALSE, FALSE, []);
		$problematicNeveFoundIgnored = new CookieSuggestionForResolving('428b6105-4c53-4ed4-8223-9c838840b056', 'Problematic_never_found_ignored', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), TRUE, FALSE, []);

		$store = $this->createStore([
			[
				FindCookieSuggestionsForResolvingQuery::create($projectId),
				[
					$missingNormal,
					$missingIgnored,

					$unproblematicNormal,
					$unproblematicWildcard,
					$unproblematicWildcard2,

					$unassociatedNormal,
					$unassociatedWildcard,
					$unassociatedIgnored,

					$problematicSingleCategory,
					$problematicSingleCategoryIgnored,
					$problematicTwoCategories,

					$problematicLongTimeNotFound,

					$problematicNeveFound,
					$problematicNeveFoundIgnored,
				],
			],
			[
				FindCookieDataForSuggestionQuery::create($projectId),
				[
					$unproblematicNormalCookie = new CookieDataForSuggestion('912b294d-cd6e-45f8-a05a-5f8c79859986', 'Unproblematic_normal', '', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'categoryA', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'providerA', 'Provider A', TRUE),
					$unproblematicWildcardCookie = new CookieDataForSuggestion('3bd7dbaf-7fb5-4867-89de-8d108497f935', 'Unproblematic_wildcard_*', '', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'categoryA', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'providerA', 'Provider A', TRUE),

					$unassociatedNormalCookie = new CookieDataForSuggestion('c8ebc9c9-aed2-4742-bfb5-d25b738e0285', 'Unassociated_normal', '', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'categoryA', '7b6348f1-9ce0-4dd4-a12c-20a5925e680c', 'providerB', 'Provider B', FALSE),
					$unassociatedWildcardCookie = new CookieDataForSuggestion('7bf422e9-9ac3-4484-97f3-36d81980f7c2', 'Unassociated_wildcard_*', '', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'categoryA', '7b6348f1-9ce0-4dd4-a12c-20a5925e680c', 'providerB', 'Provider B', FALSE),
					$unassociatedIgnoredCookie = new CookieDataForSuggestion('b59ac571-538e-4412-9888-cd5a8f834118', 'Unassociated_ignored', '', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'categoryA', '7b6348f1-9ce0-4dd4-a12c-20a5925e680c', 'providerB', 'Provider B', FALSE),

					$problematicSingleCategoryCookie = new CookieDataForSuggestion('1f5d336e-7c96-4846-921c-f39b78ed8d21', 'Problematic_single_category', '', 'example.com', '843e66bf-5702-4543-84ac-b11d709fb556', 'categoryC', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'providerA', 'Provider A', TRUE),
					$problematicSingleCategoryIgnoredCookie = new CookieDataForSuggestion('cccbb209-d4ea-4fdb-997d-285208061891', 'Problematic_single_category_ignored', '', 'example.com', '843e66bf-5702-4543-84ac-b11d709fb556', 'categoryC', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'providerA', 'Provider A', TRUE),
					$problematicTwoCategoriesCookie1 = new CookieDataForSuggestion('ac2cc255-8056-455f-92e8-716c6e17814e', 'Problematic_two_categories', '', 'example.com', '843e66bf-5702-4543-84ac-b11d709fb556', 'categoryC', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'providerA', 'Provider A', TRUE),
					$problematicTwoCategoriesCookie2 = new CookieDataForSuggestion('b9e3920f-9d67-4dd6-bd9b-1d3ce244a60c', 'Problematic_two_categories', '', 'example.com', '3cddf8e3-0f29-419b-90f4-c57176cbec42', 'categoryD', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'providerA', 'Provider A', TRUE),

					$problematicLongTimeNotFoundCookie = new CookieDataForSuggestion('71a45587-f7e2-46bd-979b-d31d0c86976b', 'Problematic_long_time_not_found', '', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'categoryA', '7b6348f1-9ce0-4dd4-a12c-20a5925e680c', 'providerB', 'Provider B', TRUE),

					$problematicNeveFoundVirtualCookie = new CookieDataForSuggestion('3acfd880-03fc-42f4-8c54-7c36164033f2', 'Problematic_never_found_virtual', '', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'categoryA', '7b6348f1-9ce0-4dd4-a12c-20a5925e680c', 'providerB', 'Provider B', TRUE),
					$problematicNeveFoundCookie = new CookieDataForSuggestion('b6c902c1-339f-4edd-b4e4-3b62185e2c69', 'Problematic_never_found', '', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'categoryA', '7b6348f1-9ce0-4dd4-a12c-20a5925e680c', 'providerB', 'Provider B', TRUE),
					$problematicNeveFoundIgnoredCookie = new CookieDataForSuggestion('bb943612-ea0b-480c-904c-9a7e080bc792', 'Problematic_never_found_ignored', '', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'categoryA', '7b6348f1-9ce0-4dd4-a12c-20a5925e680c', 'providerB', 'Provider B', TRUE),
				],
			],
		], []);

		# mock current datetime for problems resolving
		call_user_func(Closure::bind(static function () use ($store, $now) {
			$store->getNowFunction = static fn () => $now;
		}, NULL, $store));

		$suggestions = $store->resolveCookieSuggestions($projectId);
		$ignoredCookieSuggestions = $suggestions->getSuggestionsByType(IgnoredCookieSuggestion::class);
		$missingCookieSuggestions = $suggestions->getSuggestionsByType(MissingCookieSuggestion::class);
		$problematicCookieSuggestions = $suggestions->getSuggestionsByType(ProblematicCookieSuggestion::class);
		$unassociatedCookieSuggestions = $suggestions->getSuggestionsByType(UnassociatedCookieSuggestion::class);
		$unproblematicCookieSuggestions = $suggestions->getSuggestionsByType(UnproblematicCookieSuggestion::class);

		# ===== Ignored =====
		Assert::equal([
			new IgnoredCookieSuggestion(
				new MissingCookieSuggestion(
					'86a0ecaf-44ec-4847-b43b-562a966c7c86',
					'Missing_ignored',
					'example.com',
					[CookieOccurrence::fromCookieOccurrenceForResolving($missingIgnoredOccurrence1)],
					new Solutions(
						$projectId,
						'86a0ecaf-44ec-4847-b43b-562a966c7c86',
						['MissingCookieSuggestion'],
						$store->getDataStore(),
						new CreateNewCookie(),
						new IgnoreUntilNexOccurrence(),
						new IgnorePermanently(),
					),
				),
				FALSE,
				new Solutions(
					$projectId,
					'86a0ecaf-44ec-4847-b43b-562a966c7c86',
					['IgnoredCookieSuggestion'],
					$store->getDataStore(),
					new DoNotIgnore(),
				),
			),
			new IgnoredCookieSuggestion(
				new UnassociatedCookieSuggestion(
					'fa47cb13-90a8-4eb4-b2b1-30c0acc8b54b',
					'Unassociated_ignored',
					'example.com',
					[CookieOccurrence::fromCookieOccurrenceForResolving($unassociatedIgnoredOccurrence1)],
					[ExistingCookie::fromCookieDataForSuggestion($unassociatedIgnoredCookie->withMetadataField(CookieDataForSuggestion::METADATA_FIELD_SAME_DOMAIN, TRUE))],
					new Solutions(
						$projectId,
						'fa47cb13-90a8-4eb4-b2b1-30c0acc8b54b',
						['UnassociatedCookieSuggestion'],
						$store->getDataStore(),
						new SolutionGroup(
							'associate_cookie_provider_with_project',
							new AssociateCookieProviderWithProject('7b6348f1-9ce0-4dd4-a12c-20a5925e680c', 'Provider B'),
						),
						new CreateNewCookie(),
						new IgnoreUntilNexOccurrence(),
						new IgnorePermanently()
					),
				),
				TRUE,
				new Solutions(
					$projectId,
					'fa47cb13-90a8-4eb4-b2b1-30c0acc8b54b',
					['IgnoredCookieSuggestion'],
					$store->getDataStore(),
					new DoNotIgnore(),
				),
			),
			new IgnoredCookieSuggestion(
				new ProblematicCookieSuggestion(
					'46ac6172-9f8e-492a-bef7-832f5055e201',
					'Problematic_single_category_ignored',
					'example.com',
					[CookieOccurrence::fromCookieOccurrenceForResolving($problematicSingleCategoryIgnoredOccurrence1), CookieOccurrence::fromCookieOccurrenceForResolving($problematicSingleCategoryIgnoredOccurrence2)],
					[ExistingCookie::fromCookieDataForSuggestion($problematicSingleCategoryIgnoredCookie->withMetadataField(CookieDataForSuggestion::METADATA_FIELD_SAME_DOMAIN, TRUE))],
					[
						new CookieIsInCategoryThatIsNotAcceptedByScenario(
							['categoryC'],
							['categoryA', 'categoryB'],
							CookieOccurrence::fromCookieOccurrenceForResolving($problematicSingleCategoryIgnoredOccurrence2),
							new Solutions(
								$projectId,
								'46ac6172-9f8e-492a-bef7-832f5055e201',
								['561109e2-5944-44ae-aedb-dcbc58e62d55', 'ProblematicCookieSuggestion', CookieIsInCategoryThatIsNotAcceptedByScenario::TYPE],
								$store->getDataStore(),
								new SolutionGroup(
									'change_cookie_category',
									new ChangeCookieCategory('cccbb209-d4ea-4fdb-997d-285208061891', 'categoryC', 'Provider A'),
								),
								new SolutionGroup(
									'create_new_cookie_with_not_accepted_category',
									new CreateNewCookieWithNotAcceptedCategory('cccbb209-d4ea-4fdb-997d-285208061891', 'categoryC', 'Provider A'),
								),
								new IgnoreUntilNexOccurrence(),
								new IgnorePermanently(),
							),
						),
					],
				),
				TRUE,
				new Solutions(
					$projectId,
					'46ac6172-9f8e-492a-bef7-832f5055e201',
					['IgnoredCookieSuggestion'],
					$store->getDataStore(),
					new DoNotIgnore(),
				),
			),
			new IgnoredCookieSuggestion(
				new ProblematicCookieSuggestion(
					'428b6105-4c53-4ed4-8223-9c838840b056',
					'Problematic_never_found_ignored',
					'example.com',
					[],
					[ExistingCookie::fromCookieDataForSuggestion($problematicNeveFoundIgnoredCookie->withMetadataField(CookieDataForSuggestion::METADATA_FIELD_SAME_DOMAIN, TRUE))],
					[
						new CookieWasNeverFound(
							new Solutions(
								$projectId,
								'428b6105-4c53-4ed4-8223-9c838840b056',
								[
									'ProblematicCookieSuggestion',
									CookieWasNeverFound::TYPE,
								],
								$store->getDataStore(),
								new IgnoreUntilNexOccurrence(),
							)
						),
					],
				),
				FALSE,
				new Solutions(
					$projectId,
					'428b6105-4c53-4ed4-8223-9c838840b056',
					['IgnoredCookieSuggestion'],
					$store->getDataStore(),
					new DoNotIgnore(),
				),
			),
		], $ignoredCookieSuggestions);

		# ===== Missing =====
		Assert::equal([
			new MissingCookieSuggestion(
				'9189459d-f5f5-41b9-b1fd-b4a6cc914ebc',
				'Missing_normal',
				'example.com',
				[CookieOccurrence::fromCookieOccurrenceForResolving($missingNormalOccurrence1)],
				new Solutions(
					$projectId,
					'9189459d-f5f5-41b9-b1fd-b4a6cc914ebc',
					['MissingCookieSuggestion'],
					$store->getDataStore(),
					new CreateNewCookie(),
					new IgnoreUntilNexOccurrence(),
					new IgnorePermanently()
				),
			),
		], $missingCookieSuggestions);

		# ===== Unproblematic =====
		Assert::equal([
			new UnproblematicCookieSuggestion(
				'17af2175-7970-436f-8ae5-d36400d17b2a',
				'Unproblematic_normal',
				'example.com',
				[CookieOccurrence::fromCookieOccurrenceForResolving($unproblematicNormalOccurrence1)],
				[ExistingCookie::fromCookieDataForSuggestion($unproblematicNormalCookie->withMetadataField(CookieDataForSuggestion::METADATA_FIELD_SAME_DOMAIN, TRUE))],
			),
			new UnproblematicCookieSuggestion(
				'0007e99a-49a4-4c63-9eaf-eb6464cc9d04',
				'Unproblematic_wildcard_*',
				'example.com',
				[CookieOccurrence::fromCookieOccurrenceForResolving($unproblematicWildcardOccurrence1), CookieOccurrence::fromCookieOccurrenceForResolving($unproblematicWildcard2Occurrence1)],
				[ExistingCookie::fromCookieDataForSuggestion($unproblematicWildcardCookie->withMetadataField(CookieDataForSuggestion::METADATA_FIELD_SAME_DOMAIN, TRUE))],
			),
		], $unproblematicCookieSuggestions);

		# ===== Unassociated =====
		Assert::equal([
			new UnassociatedCookieSuggestion(
				'9df8977b-9d44-46be-94ea-f6399cc327b1',
				'Unassociated_normal',
				'example.com',
				[CookieOccurrence::fromCookieOccurrenceForResolving($unassociatedNormalOccurrence1)],
				[ExistingCookie::fromCookieDataForSuggestion($unassociatedNormalCookie->withMetadataField(CookieDataForSuggestion::METADATA_FIELD_SAME_DOMAIN, TRUE))],
				new Solutions(
					$projectId,
					'9df8977b-9d44-46be-94ea-f6399cc327b1',
					['UnassociatedCookieSuggestion'],
					$store->getDataStore(),
					new SolutionGroup(
						'associate_cookie_provider_with_project',
						new AssociateCookieProviderWithProject('7b6348f1-9ce0-4dd4-a12c-20a5925e680c', 'Provider B'),
					),
					new CreateNewCookie(),
					new IgnoreUntilNexOccurrence(),
					new IgnorePermanently(),
				),
			),
			new UnassociatedCookieSuggestion(
				'0d5db7d0-b81d-4514-98e5-04a218451d17',
				'Unassociated_wildcard_*',
				'example.com',
				[CookieOccurrence::fromCookieOccurrenceForResolving($unassociatedWildcardOccurrence1)],
				[ExistingCookie::fromCookieDataForSuggestion($unassociatedWildcardCookie->withMetadataField(CookieDataForSuggestion::METADATA_FIELD_SAME_DOMAIN, TRUE))],
				new Solutions(
					$projectId,
					'0d5db7d0-b81d-4514-98e5-04a218451d17',
					['UnassociatedCookieSuggestion'],
					$store->getDataStore(),
					new SolutionGroup(
						'associate_cookie_provider_with_project',
						new AssociateCookieProviderWithProject('7b6348f1-9ce0-4dd4-a12c-20a5925e680c', 'Provider B'),
					),
					new CreateNewCookie(),
					new IgnoreUntilNexOccurrence(),
					new IgnorePermanently(),
				),
			),
		], $unassociatedCookieSuggestions);

		# ===== Problematic =====
		Assert::equal([
			new ProblematicCookieSuggestion(
				'9ff5b3b4-6b70-40d7-ae7f-2122651a70b4',
				'Problematic_long_time_not_found',
				'example.com',
				[CookieOccurrence::fromCookieOccurrenceForResolving($problematicLongTimeNotFoundOccurrence1), CookieOccurrence::fromCookieOccurrenceForResolving($problematicLongTimeNotFoundOccurrence2)],
				[ExistingCookie::fromCookieDataForSuggestion($problematicLongTimeNotFoundCookie->withMetadataField(CookieDataForSuggestion::METADATA_FIELD_SAME_DOMAIN, TRUE))],
				[
					new CookieLongTimeNotFound(
						15,
						new Solutions(
							$projectId,
							'9ff5b3b4-6b70-40d7-ae7f-2122651a70b4',
							[
								'ProblematicCookieSuggestion',
								CookieLongTimeNotFound::TYPE,
							],
							$store->getDataStore(),
							new IgnoreUntilNexOccurrence(),
						)
					),
				]
			),
			new ProblematicCookieSuggestion(
				'9df8977b-9d44-46be-94ea-f6399cc327b1',
				'Problematic_single_category',
				'example.com',
				[CookieOccurrence::fromCookieOccurrenceForResolving($problematicSingleCategoryOccurrence1), CookieOccurrence::fromCookieOccurrenceForResolving($problematicSingleCategoryOccurrence2)],
				[ExistingCookie::fromCookieDataForSuggestion($problematicSingleCategoryCookie->withMetadataField(CookieDataForSuggestion::METADATA_FIELD_SAME_DOMAIN, TRUE))],
				[
					new CookieIsInCategoryThatIsNotAcceptedByScenario(
						['categoryC'],
						['categoryA', 'categoryB'],
						CookieOccurrence::fromCookieOccurrenceForResolving($problematicSingleCategoryOccurrence2),
						new Solutions(
							$projectId,
							'9df8977b-9d44-46be-94ea-f6399cc327b1',
							['561109e2-5944-44ae-aedb-dcbc58e62d55', 'ProblematicCookieSuggestion', CookieIsInCategoryThatIsNotAcceptedByScenario::TYPE],
							$store->getDataStore(),
							new SolutionGroup(
								'change_cookie_category',
								new ChangeCookieCategory('1f5d336e-7c96-4846-921c-f39b78ed8d21', 'categoryC', 'Provider A'),
							),
							new SolutionGroup(
								'create_new_cookie_with_not_accepted_category',
								new CreateNewCookieWithNotAcceptedCategory('1f5d336e-7c96-4846-921c-f39b78ed8d21', 'categoryC', 'Provider A'),
							),
							new IgnoreUntilNexOccurrence(),
							new IgnorePermanently(),
						),
					),
				],
			),
			new ProblematicCookieSuggestion(
				'42c660ce-6770-4002-abd8-338a4015444d',
				'Problematic_two_categories',
				'example.com',
				[CookieOccurrence::fromCookieOccurrenceForResolving($problematicTwoCategoriesOccurrence2), CookieOccurrence::fromCookieOccurrenceForResolving($problematicTwoCategoriesOccurrence1)],
				[
					ExistingCookie::fromCookieDataForSuggestion($problematicTwoCategoriesCookie1->withMetadataField(CookieDataForSuggestion::METADATA_FIELD_SAME_DOMAIN, TRUE)),
					ExistingCookie::fromCookieDataForSuggestion($problematicTwoCategoriesCookie2->withMetadataField(CookieDataForSuggestion::METADATA_FIELD_SAME_DOMAIN, TRUE)),
				],
				[
					new CookieIsInCategoryThatIsNotAcceptedByScenario(
						['categoryC', 'categoryD'],
						['categoryA', 'categoryB'],
						CookieOccurrence::fromCookieOccurrenceForResolving($problematicTwoCategoriesOccurrence1),
						new Solutions(
							$projectId,
							'42c660ce-6770-4002-abd8-338a4015444d',
							['babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ProblematicCookieSuggestion', CookieIsInCategoryThatIsNotAcceptedByScenario::TYPE],
							$store->getDataStore(),
							new SolutionGroup(
								'change_cookie_category',
								new ChangeCookieCategory('ac2cc255-8056-455f-92e8-716c6e17814e', 'categoryC', 'Provider A'),
								new ChangeCookieCategory('b9e3920f-9d67-4dd6-bd9b-1d3ce244a60c', 'categoryD', 'Provider A'),
							),
							new SolutionGroup(
								'create_new_cookie_with_not_accepted_category',
								new CreateNewCookieWithNotAcceptedCategory('ac2cc255-8056-455f-92e8-716c6e17814e', 'categoryC', 'Provider A'),
								new CreateNewCookieWithNotAcceptedCategory('b9e3920f-9d67-4dd6-bd9b-1d3ce244a60c', 'categoryD', 'Provider A'),
							),
							new IgnoreUntilNexOccurrence(),
							new IgnorePermanently(),
						),
					),
				],
			),
			new ProblematicCookieSuggestion(
				'105da7b4-2fbd-475f-bb63-1b18342f782a',
				'Problematic_never_found',
				'example.com',
				[],
				[ExistingCookie::fromCookieDataForSuggestion($problematicNeveFoundCookie->withMetadataField(CookieDataForSuggestion::METADATA_FIELD_SAME_DOMAIN, TRUE))],
				[
					new CookieWasNeverFound(
						new Solutions(
							$projectId,
							'105da7b4-2fbd-475f-bb63-1b18342f782a',
							[
								'ProblematicCookieSuggestion',
								CookieWasNeverFound::TYPE,
							],
							$store->getDataStore(),
							new IgnoreUntilNexOccurrence(),
						)
					),
				],
			),
			new ProblematicCookieSuggestion(
				'3acfd880-03fc-42f4-8c54-7c36164033f2',
				'Problematic_never_found_virtual',
				'example.com',
				[],
				[ExistingCookie::fromCookieDataForSuggestion($problematicNeveFoundVirtualCookie->withMetadataField(CookieDataForSuggestion::METADATA_FIELD_SAME_DOMAIN, TRUE))],
				[
					new CookieWasNeverFound(
						new Solutions(
							$projectId,
							'3acfd880-03fc-42f4-8c54-7c36164033f2',
							[
								'ProblematicCookieSuggestion',
								CookieWasNeverFound::TYPE,
							],
							$store->getDataStore(),
							new IgnoreUntilNexOccurrence(TRUE),
						)
					),
				],
				TRUE,
			),
		], $problematicCookieSuggestions);
	}

	protected function tearDown(): void
	{
		Mockery::close();
	}

	private function createStore(array $queriesPairedWithResults, array $commands): CookieSuggestionsStore
	{
		return new CookieSuggestionsStore(
			$this->createCommandBus($commands),
			$this->createQueryBus($queriesPairedWithResults),
			new MemoryDataStore(),
			NULL,
			14,
		);
	}

	private function createQueryBus(array $queriesPairedWithResults)
	{
		$queryBus = Mockery::mock(QueryBusInterface::class);

		foreach ($queriesPairedWithResults as [$query, $result]) {
			$queryBus->expects('dispatch')
				->once()
				->with(IsEqual::equalTo($query))
				->andReturn($result);
		}

		return $queryBus;
	}

	private function createCommandBus(array $commands)
	{
		$queryBus = Mockery::mock(CommandBusInterface::class);

		foreach ($commands as $command) {
			$queryBus->expects('dispatch')
				->once()
				->with(IsEqual::equalTo($command))
				->andReturnUndefined();
		}

		return $queryBus;
	}

	private function createCookieToStore(string $name, string $domain, string $foundOnUrl): CrawlerClientCookie
	{
		return new CrawlerClientCookie(
			uniqid(),
			$name,
			$domain,
			FALSE,
			FALSE,
			FALSE,
			'Lax',
			$foundOnUrl,
		);
	}
}

(new CookieSuggestionStoreTest())->run();
