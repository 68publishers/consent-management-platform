<?php

declare(strict_types=1);

namespace App\Tests\Application\CookieSuggestion;

use Tester\Assert;
use Tester\TestCase;
use Mockery;
use App\ReadModel\Cookie\CookieDataForSuggestion;
use App\ReadModel\CookieSuggestion\CookieSuggestion;
use App\Application\CookieSuggestion\Solution\Solutions;
use App\Application\CookieSuggestion\Solution\DoNotIgnore;
use App\Application\CookieSuggestion\Solution\CreateNewCookie;
use App\Application\CookieSuggestion\DataStore\MemoryDataStore;
use App\Application\CookieSuggestion\Suggestion\ExistingCookie;
use App\ReadModel\CookieSuggestion\CookieSuggestionForResolving;
use App\ReadModel\CookieSuggestion\CookieOccurrenceForResolving;
use App\Application\CookieSuggestion\Warning\CookieDomainNotSet;
use App\Application\CookieSuggestion\Solution\IgnorePermanently;
use App\Application\CookieSuggestion\Suggestion\CookieOccurrence;
use App\Application\CookieSuggestion\Solution\ChangeCookieCategory;
use App\Domain\CookieSuggestion\Command\CreateCookieSuggestionCommand;
use App\Application\CookieSuggestion\Solution\IgnoreUntilNexOccurrence;
use App\Application\CookieSuggestion\Suggestion\MissingCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\IgnoredCookieSuggestion;
use App\ReadModel\CookieSuggestion\FindCookieSuggestionsForResolvingQuery;
use App\Application\CookieSuggestion\Suggestion\ProblematicCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\UnassociatedCookieSuggestion;
use App\Application\CookieSuggestion\Suggestion\UnproblematicCookieSuggestion;
use App\Application\CookieSuggestion\Solution\AssociateCookieProviderWithProject;
use App\Domain\CookieSuggestion\Command\CookieOccurrence as CommandCookieOccurrence;
use App\ReadModel\Cookie\FindCookieDataForSuggestionQuery;
use App\Application\CookieSuggestion\CookieSuggestionsStore;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use DateTimeImmutable;
use DateTimeZone;
use DateTimeInterface;
use App\Domain\CookieSuggestion\Command\AddCookieSuggestionOccurrencesCommand;
use App\Application\CookieSuggestion\Solution\CreateNewCookieWithNotAcceptedCategory;
use App\ReadModel\CookieSuggestion\GetCookieSuggestionByProjectIdAndNameAndDomainQuery;
use App\Application\CookieSuggestion\Problem\CookieIsInCategoryThatIsNotAcceptedByScenario;
use SixtyEightPublishers\CrawlerClient\Controller\Scenario\ValueObject\Cookie as CrawlerClientCookie;
use Hamcrest\Core\IsEqual;

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
                        new CookieDataForSuggestion('912b294d-cd6e-45f8-a05a-5f8c79859986', 'ExistingCookie', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'test_category', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'test_provider', 'Test provider', true),
                        new CookieDataForSuggestion('3bd7dbaf-7fb5-4867-89de-8d108497f935', 'AssociatedWildcardCookie*', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'test_category', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'test_provider', 'Test provider', true),
                        new CookieDataForSuggestion('f85b68cd-0fc9-4ca6-9517-a63bc01f5749', 'NonAssociatedWildcardCookie*', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'test_category', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'test_provider', 'Test provider', false),
                        new CookieDataForSuggestion('85ad37ea-b319-4e30-b754-e87f567b0455', 'WildcardCookieWithDifferentDomain_*', 'google.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'test_category', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'test_provider', 'Test provider', true),
                        new CookieDataForSuggestion('91aa5ec3-220e-4bd3-b793-a64697300b9a', 'ExistingCookieWithEmptyDomain', '', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'test_category', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'test_provider', 'Test provider', true),
                        new CookieDataForSuggestion('4dff0d51-c23f-4670-b617-540f73beb410', 'WildcardCookieWithEmptyDomain_*', '', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'test_category', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'test_provider', 'Test provider', true),
                    ]
                ],
                [
                    GetCookieSuggestionByProjectIdAndNameAndDomainQuery::create($projectId, 'ExistingCookie', 'example.com'),
                    new CookieSuggestion('f17fdace-f261-4cda-9524-ab4b7e6f1968', $projectId, 'ExistingCookie', 'example.com', new DateTimeImmutable('2023-06-27 12:00:00')),
                ],
                [
                    GetCookieSuggestionByProjectIdAndNameAndDomainQuery::create($projectId, 'MissingCookie', 'example.com'),
                    null,
                ],
                [
                    GetCookieSuggestionByProjectIdAndNameAndDomainQuery::create($projectId, 'AssociatedWildcardCookie*', 'example.com'),
                    new CookieSuggestion('13e2446e-d61b-430e-8085-c96f99e82b41', $projectId, 'AssociatedWildcardCookie*', 'example.com', new DateTimeImmutable('2023-06-27 13:00:00')),
                ],
                [
                    GetCookieSuggestionByProjectIdAndNameAndDomainQuery::create($projectId, 'NonAssociatedWildcardCookie987654', 'example.com'),
                    null,
                ],
                [
                    GetCookieSuggestionByProjectIdAndNameAndDomainQuery::create($projectId, 'WildcardCookieWithDifferentDomain_123456', 'example.com'),
                    null,
                ],
                [
                    GetCookieSuggestionByProjectIdAndNameAndDomainQuery::create($projectId, 'ExistingCookieWithEmptyDomain', 'example.com'),
                    null,
                ],
                [
                    GetCookieSuggestionByProjectIdAndNameAndDomainQuery::create($projectId, 'WildcardCookieWithEmptyDomain_98765', 'example.com'),
                    null,
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
                CreateCookieSuggestionCommand::create($projectId, 'WildcardCookieWithEmptyDomain_98765', 'example.com', [
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
        Assert::true(true);
    }

    public function testCookieSuggestionsResolving(): void
    {
        $projectId = '1786dd53-4493-4e52-aefe-059863757c70';
        $createdAt = new DateTimeImmutable('2023-06-27 12:00:00', new DateTimeZone('UTC'));

        # ===== Missing =====
        $missingNormal = new CookieSuggestionForResolving('9189459d-f5f5-41b9-b1fd-b4a6cc914ebc', 'Missing_normal', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), false, false, [
            $missingNormalOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Missing_normal', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
        ]);
        $missingIgnored = new CookieSuggestionForResolving('86a0ecaf-44ec-4847-b43b-562a966c7c86', 'Missing_ignored', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), true, false, [
            $missingIgnoredOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Missing_ignored', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
        ]);

        # ===== Unproblematic =====
        $unproblematicNormal = new CookieSuggestionForResolving('17af2175-7970-436f-8ae5-d36400d17b2a', 'Unproblematic_normal', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), false, false, [
            $unproblematicNormalOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Unproblematic_normal', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
        ]);
        $unproblematicWildcard = new CookieSuggestionForResolving('0007e99a-49a4-4c63-9eaf-eb6464cc9d04', 'Unproblematic_wildcard_12234', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), false, false, [
            $unproblematicWildcardOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Unproblematic_wildcard_12234', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
        ]);
        $unproblematicWildcard2 = new CookieSuggestionForResolving('0007e99a-49a4-4c63-9eaf-eb6464cc9d04', 'Unproblematic_wildcard_*', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), false, false, [
            $unproblematicWildcard2Occurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Unproblematic_wildcard_*', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
        ]);
        $unproblematicNoDomain = new CookieSuggestionForResolving('37c9d6c6-e7b4-48bc-8341-14695a63cbc1', 'Unproblematic_no_domain', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), false, false, [
            $unproblematicNoDomainOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Unproblematic_no_domain', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
        ]);

        # ===== Unassociated =====
        $unassociatedNormal = new CookieSuggestionForResolving('9df8977b-9d44-46be-94ea-f6399cc327b1', 'Unassociated_normal', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), false, false, [
            $unassociatedNormalOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Unassociated_normal', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
        ]);
        $unassociatedWildcard = new CookieSuggestionForResolving('0d5db7d0-b81d-4514-98e5-04a218451d17', 'Unassociated_wildcard_34556a', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), false, false, [
            $unassociatedWildcardOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Unassociated_wildcard_34556a', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
        ]);
        $unassociatedIgnored = new CookieSuggestionForResolving('fa47cb13-90a8-4eb4-b2b1-30c0acc8b54b', 'Unassociated_ignored', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), false, true, [
            $unassociatedIgnoredOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Unassociated_ignored', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
        ]);
        $unassociatedNoDomain = new CookieSuggestionForResolving('9df8977b-9d44-46be-94ea-f6399cc327b1', 'Unassociated_no_domain', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), false, false, [
            $unassociatedNoDomainOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Unassociated_no_domain', 'https://www.example.com/page1', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 12:00:00')),
        ]);

        # ===== Problematic =====
        $problematicSingleCategory = new CookieSuggestionForResolving('9df8977b-9d44-46be-94ea-f6399cc327b1', 'Problematic_single_category', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), false, false, [
            $problematicSingleCategoryOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Problematic_single_category', 'https://www.example.com/page1', ['categoryA', 'categoryB', 'categoryC'], new DateTimeImmutable('2023-06-24 12:00:00')),
            $problematicSingleCategoryOccurrence2 = new CookieOccurrenceForResolving('561109e2-5944-44ae-aedb-dcbc58e62d55', 'ScenarioB', 'Problematic_single_category', 'https://www.example.com/page2', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 14:00:00')),
        ]);
        $problematicSingleCategoryIgnored = new CookieSuggestionForResolving('46ac6172-9f8e-492a-bef7-832f5055e201', 'Problematic_single_category_ignored', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), true, true, [
            $problematicSingleCategoryIgnoredOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Problematic_single_category_ignored', 'https://www.example.com/page1', ['categoryA', 'categoryB', 'categoryC'], new DateTimeImmutable('2023-06-24 12:00:00')),
            $problematicSingleCategoryIgnoredOccurrence2 = new CookieOccurrenceForResolving('561109e2-5944-44ae-aedb-dcbc58e62d55', 'ScenarioB', 'Problematic_single_category_ignored', 'https://www.example.com/page2', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 14:00:00')),
        ]);
        $problematicSingleCategoryMissingDomain = new CookieSuggestionForResolving('43cc8ac0-24b9-4489-9ccf-b1c5bae3e4ef', 'Problematic_single_category_missing_domain', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), false, false, [
            $problematicSingleCategoryMissingDomainOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Problematic_single_category_missing_domain', 'https://www.example.com/page1', ['categoryA', 'categoryB', 'categoryC'], new DateTimeImmutable('2023-06-24 12:00:00')),
            $problematicSingleCategoryMissingDomainOccurrence2 = new CookieOccurrenceForResolving('561109e2-5944-44ae-aedb-dcbc58e62d55', 'ScenarioB', 'Problematic_single_category_missing_domain', 'https://www.example.com/page2', ['categoryA', 'categoryB'], new DateTimeImmutable('2023-06-24 14:00:00')),
        ]);
        $problematicTwoCategories = new CookieSuggestionForResolving('42c660ce-6770-4002-abd8-338a4015444d', 'Problematic_two_categories', 'example.com', $createdAt = $createdAt->modify('+ 1 second'), false, false, [
            $problematicTwoCategoriesOccurrence1 = new CookieOccurrenceForResolving('babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ScenarioA', 'Problematic_two_categories', 'https://www.example.com/page1', ['categoryA', 'categoryB', 'categoryC'], new DateTimeImmutable('2023-06-24 12:00:00')),
            $problematicTwoCategoriesOccurrence2 = new CookieOccurrenceForResolving('561109e2-5944-44ae-aedb-dcbc58e62d55', 'ScenarioB', 'Problematic_two_categories', 'https://www.example.com/page2', ['categoryA', 'categoryB', 'categoryD'], new DateTimeImmutable('2023-06-24 14:00:00')),
        ]);

        $store = $this->createStore([
            [
                FindCookieSuggestionsForResolvingQuery::create($projectId),
                [
                    $missingNormal,
                    $missingIgnored,

                    $unproblematicNormal,
                    $unproblematicWildcard,
                    $unproblematicWildcard2,
                    $unproblematicNoDomain,

                    $unassociatedNormal,
                    $unassociatedWildcard,
                    $unassociatedIgnored,
                    $unassociatedNoDomain,

                    $problematicSingleCategory,
                    $problematicSingleCategoryIgnored,
                    $problematicSingleCategoryMissingDomain,
                    $problematicTwoCategories,
                ],
            ],
            [
                FindCookieDataForSuggestionQuery::create($projectId),
                [
                    $unproblematicNormalCookie = new CookieDataForSuggestion('912b294d-cd6e-45f8-a05a-5f8c79859986', 'Unproblematic_normal', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'categoryA', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'providerA', 'Provider A', true),
                    $unproblematicWildcardCookie = new CookieDataForSuggestion('3bd7dbaf-7fb5-4867-89de-8d108497f935', 'Unproblematic_wildcard_*', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'categoryA', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'providerA', 'Provider A', true),
                    $unproblematicNoDomainCookie = new CookieDataForSuggestion('3bd7dbaf-7fb5-4867-89de-8d108497f935', 'Unproblematic_no_domain', '', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'categoryA', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'providerA', 'Provider A', true),

                    $unassociatedNormalCookie = new CookieDataForSuggestion('c8ebc9c9-aed2-4742-bfb5-d25b738e0285', 'Unassociated_normal', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'categoryA', '7b6348f1-9ce0-4dd4-a12c-20a5925e680c', 'providerB', 'Provider B', false),
                    $unassociatedWildcardCookie = new CookieDataForSuggestion('7bf422e9-9ac3-4484-97f3-36d81980f7c2', 'Unassociated_wildcard_*', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'categoryA', '7b6348f1-9ce0-4dd4-a12c-20a5925e680c', 'providerB', 'Provider B', false),
                    $unassociatedIgnoredCookie = new CookieDataForSuggestion('b59ac571-538e-4412-9888-cd5a8f834118', 'Unassociated_ignored', 'example.com', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'categoryA', '7b6348f1-9ce0-4dd4-a12c-20a5925e680c', 'providerB', 'Provider B', false),
                    $unassociatedNoDomainCookie = new CookieDataForSuggestion('30b87e4d-cfe7-4699-a70c-5d9ddc34deb7', 'Unassociated_no_domain', '', '5c94a85d-27ac-47ac-8c00-0a4d70f5aabb', 'categoryA', '7b6348f1-9ce0-4dd4-a12c-20a5925e680c', 'providerB', 'Provider B', false),

                    $problematicSingleCategoryCookie = new CookieDataForSuggestion('1f5d336e-7c96-4846-921c-f39b78ed8d21', 'Problematic_single_category', 'example.com', '843e66bf-5702-4543-84ac-b11d709fb556', 'categoryC', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'providerA', 'Provider A', true),
                    $problematicSingleCategoryIgnoredCookie = new CookieDataForSuggestion('cccbb209-d4ea-4fdb-997d-285208061891', 'Problematic_single_category_ignored', 'example.com', '843e66bf-5702-4543-84ac-b11d709fb556', 'categoryC', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'providerA', 'Provider A', true),
                    $problematicSingleCategoryMissingDomainCookie = new CookieDataForSuggestion('997637a7-595e-4242-8ccd-7dc1476aeb63', 'Problematic_single_category_missing_domain', '', '843e66bf-5702-4543-84ac-b11d709fb556', 'categoryC', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'providerA', 'Provider A', true),
                    $problematicTwoCategoriesCookie1 = new CookieDataForSuggestion('ac2cc255-8056-455f-92e8-716c6e17814e', 'Problematic_two_categories', 'example.com', '843e66bf-5702-4543-84ac-b11d709fb556', 'categoryC', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'providerA', 'Provider A', true),
                    $problematicTwoCategoriesCookie2 = new CookieDataForSuggestion('b9e3920f-9d67-4dd6-bd9b-1d3ce244a60c', 'Problematic_two_categories', 'example.com', '3cddf8e3-0f29-419b-90f4-c57176cbec42', 'categoryD', '787f3813-db25-4a4a-9f69-16f256fd92ac', 'providerA', 'Provider A', true),
                ],
            ],
        ], []);

        $suggestions = $store->resolveCookieSuggestions($projectId);
        $ignoredCookieSuggestions = $suggestions->getSuggestions(IgnoredCookieSuggestion::class);
        $missingCookieSuggestions = $suggestions->getSuggestions(MissingCookieSuggestion::class);
        $problematicCookieSuggestions = $suggestions->getSuggestions(ProblematicCookieSuggestion::class);
        $unassociatedCookieSuggestions = $suggestions->getSuggestions(UnassociatedCookieSuggestion::class);
        $unproblematicCookieSuggestions = $suggestions->getSuggestions(UnproblematicCookieSuggestion::class);

        # ===== Ignored =====
        Assert::equal([
            new IgnoredCookieSuggestion(
                new MissingCookieSuggestion(
                    '86a0ecaf-44ec-4847-b43b-562a966c7c86',
                    'Missing_ignored',
                    'example.com',
                    [CookieOccurrence::fromCookieOccurrenceForResolving($missingIgnoredOccurrence1)],
                    [],
                    new Solutions([$projectId, '86a0ecaf-44ec-4847-b43b-562a966c7c86', 'MissingCookieSuggestion'], $store->getDataStore(), new CreateNewCookie(), new IgnoreUntilNexOccurrence(), new IgnorePermanently()),
                ),
                false,
                new Solutions(
                    [$projectId, '86a0ecaf-44ec-4847-b43b-562a966c7c86', 'IgnoredCookieSuggestion'],
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
                    [],
                    ExistingCookie::fromCookieDataForSuggestion($unassociatedIgnoredCookie),
                    new Solutions([$projectId, 'fa47cb13-90a8-4eb4-b2b1-30c0acc8b54b', 'b59ac571-538e-4412-9888-cd5a8f834118', 'UnassociatedCookieSuggestion'], $store->getDataStore(), new AssociateCookieProviderWithProject('7b6348f1-9ce0-4dd4-a12c-20a5925e680c'), new CreateNewCookie(), new IgnoreUntilNexOccurrence(), new IgnorePermanently()),
                ),
                true,
                new Solutions(
                    [$projectId, 'fa47cb13-90a8-4eb4-b2b1-30c0acc8b54b', 'b59ac571-538e-4412-9888-cd5a8f834118', 'IgnoredCookieSuggestion'],
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
                    [],
                    ExistingCookie::fromCookieDataForSuggestion($problematicSingleCategoryIgnoredCookie),
                    [
                        new CookieIsInCategoryThatIsNotAcceptedByScenario(
                            'categoryC',
                            ['categoryA', 'categoryB'],
                            CookieOccurrence::fromCookieOccurrenceForResolving($problematicSingleCategoryIgnoredOccurrence2),
                            new Solutions(
                                [$projectId, '46ac6172-9f8e-492a-bef7-832f5055e201', 'cccbb209-d4ea-4fdb-997d-285208061891', '561109e2-5944-44ae-aedb-dcbc58e62d55', 'ProblematicCookieSuggestion', CookieIsInCategoryThatIsNotAcceptedByScenario::TYPE],
                                $store->getDataStore(),
                                new ChangeCookieCategory('cccbb209-d4ea-4fdb-997d-285208061891'),
                                new CreateNewCookieWithNotAcceptedCategory('cccbb209-d4ea-4fdb-997d-285208061891'),
                                new IgnoreUntilNexOccurrence(),
                                new IgnorePermanently(),
                            ),
                        )
                    ],
                ),
                true,
                new Solutions(
                    [$projectId, '46ac6172-9f8e-492a-bef7-832f5055e201', 'cccbb209-d4ea-4fdb-997d-285208061891', 'IgnoredCookieSuggestion'],
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
                [],
                new Solutions([$projectId, '9189459d-f5f5-41b9-b1fd-b4a6cc914ebc', 'MissingCookieSuggestion'], $store->getDataStore(), new CreateNewCookie(), new IgnoreUntilNexOccurrence(), new IgnorePermanently()),
            )
        ], $missingCookieSuggestions);

        # ===== Unproblematic =====
        Assert::equal([
            new UnproblematicCookieSuggestion(
                '17af2175-7970-436f-8ae5-d36400d17b2a',
                'Unproblematic_normal',
                'example.com',
                [CookieOccurrence::fromCookieOccurrenceForResolving($unproblematicNormalOccurrence1)],
                [],
                ExistingCookie::fromCookieDataForSuggestion($unproblematicNormalCookie),
            ),
            new UnproblematicCookieSuggestion(
                '0007e99a-49a4-4c63-9eaf-eb6464cc9d04',
                'Unproblematic_wildcard_*',
                'example.com',
                [CookieOccurrence::fromCookieOccurrenceForResolving($unproblematicWildcardOccurrence1), CookieOccurrence::fromCookieOccurrenceForResolving($unproblematicWildcard2Occurrence1)],
                [],
                ExistingCookie::fromCookieDataForSuggestion($unproblematicWildcardCookie),
            ),
            new UnproblematicCookieSuggestion(
                '37c9d6c6-e7b4-48bc-8341-14695a63cbc1',
                'Unproblematic_no_domain',
                'example.com',
                [CookieOccurrence::fromCookieOccurrenceForResolving($unproblematicNoDomainOccurrence1)],
                [new CookieDomainNotSet()],
                ExistingCookie::fromCookieDataForSuggestion($unproblematicNoDomainCookie),
            ),
        ], $unproblematicCookieSuggestions);

        # ===== Unassociated =====
        Assert::equal([
            new UnassociatedCookieSuggestion(
                '9df8977b-9d44-46be-94ea-f6399cc327b1',
                'Unassociated_normal',
                'example.com',
                [CookieOccurrence::fromCookieOccurrenceForResolving($unassociatedNormalOccurrence1)],
                [],
                ExistingCookie::fromCookieDataForSuggestion($unassociatedNormalCookie),
                new Solutions([$projectId, '9df8977b-9d44-46be-94ea-f6399cc327b1', 'c8ebc9c9-aed2-4742-bfb5-d25b738e0285', 'UnassociatedCookieSuggestion'], $store->getDataStore(), new AssociateCookieProviderWithProject('7b6348f1-9ce0-4dd4-a12c-20a5925e680c'), new CreateNewCookie(), new IgnoreUntilNexOccurrence(), new IgnorePermanently()),
            ),
            new UnassociatedCookieSuggestion(
                '0d5db7d0-b81d-4514-98e5-04a218451d17',
                'Unassociated_wildcard_*',
                'example.com',
                [CookieOccurrence::fromCookieOccurrenceForResolving($unassociatedWildcardOccurrence1)],
                [],
                ExistingCookie::fromCookieDataForSuggestion($unassociatedWildcardCookie),
                new Solutions([$projectId, '0d5db7d0-b81d-4514-98e5-04a218451d17', '7bf422e9-9ac3-4484-97f3-36d81980f7c2', 'UnassociatedCookieSuggestion'], $store->getDataStore(), new AssociateCookieProviderWithProject('7b6348f1-9ce0-4dd4-a12c-20a5925e680c'), new CreateNewCookie(), new IgnoreUntilNexOccurrence(), new IgnorePermanently()),
            ),
            new UnassociatedCookieSuggestion(
                '9df8977b-9d44-46be-94ea-f6399cc327b1',
                'Unassociated_no_domain',
                'example.com',
                [CookieOccurrence::fromCookieOccurrenceForResolving($unassociatedNoDomainOccurrence1)],
                [new CookieDomainNotSet()],
                ExistingCookie::fromCookieDataForSuggestion($unassociatedNoDomainCookie),
                new Solutions([$projectId, '9df8977b-9d44-46be-94ea-f6399cc327b1', '30b87e4d-cfe7-4699-a70c-5d9ddc34deb7', 'UnassociatedCookieSuggestion'], $store->getDataStore(), new AssociateCookieProviderWithProject('7b6348f1-9ce0-4dd4-a12c-20a5925e680c'), new CreateNewCookie(), new IgnoreUntilNexOccurrence(), new IgnorePermanently()),
            ),
        ], $unassociatedCookieSuggestions);

        # ===== Problematic =====
        Assert::equal([
            new ProblematicCookieSuggestion(
                '9df8977b-9d44-46be-94ea-f6399cc327b1',
                'Problematic_single_category',
                'example.com',
                [CookieOccurrence::fromCookieOccurrenceForResolving($problematicSingleCategoryOccurrence1), CookieOccurrence::fromCookieOccurrenceForResolving($problematicSingleCategoryOccurrence2)],
                [],
                ExistingCookie::fromCookieDataForSuggestion($problematicSingleCategoryCookie),
                [
                    new CookieIsInCategoryThatIsNotAcceptedByScenario(
                        'categoryC',
                        ['categoryA', 'categoryB'],
                        CookieOccurrence::fromCookieOccurrenceForResolving($problematicSingleCategoryOccurrence2),
                        new Solutions(
                            [$projectId, '9df8977b-9d44-46be-94ea-f6399cc327b1', '1f5d336e-7c96-4846-921c-f39b78ed8d21', '561109e2-5944-44ae-aedb-dcbc58e62d55', 'ProblematicCookieSuggestion', CookieIsInCategoryThatIsNotAcceptedByScenario::TYPE],
                            $store->getDataStore(),
                            new ChangeCookieCategory('1f5d336e-7c96-4846-921c-f39b78ed8d21'),
                            new CreateNewCookieWithNotAcceptedCategory('1f5d336e-7c96-4846-921c-f39b78ed8d21'),
                            new IgnoreUntilNexOccurrence(),
                            new IgnorePermanently(),
                        ),
                    )
                ],
            ),
            new ProblematicCookieSuggestion(
                '43cc8ac0-24b9-4489-9ccf-b1c5bae3e4ef',
                'Problematic_single_category_missing_domain',
                'example.com',
                [CookieOccurrence::fromCookieOccurrenceForResolving($problematicSingleCategoryMissingDomainOccurrence1), CookieOccurrence::fromCookieOccurrenceForResolving($problematicSingleCategoryMissingDomainOccurrence2)],
                [new CookieDomainNotSet()],
                ExistingCookie::fromCookieDataForSuggestion($problematicSingleCategoryMissingDomainCookie),
                [
                    new CookieIsInCategoryThatIsNotAcceptedByScenario(
                        'categoryC',
                        ['categoryA', 'categoryB'],
                        CookieOccurrence::fromCookieOccurrenceForResolving($problematicSingleCategoryMissingDomainOccurrence2),
                        new Solutions(
                            [$projectId, '43cc8ac0-24b9-4489-9ccf-b1c5bae3e4ef', '997637a7-595e-4242-8ccd-7dc1476aeb63', '561109e2-5944-44ae-aedb-dcbc58e62d55', 'ProblematicCookieSuggestion', CookieIsInCategoryThatIsNotAcceptedByScenario::TYPE],
                            $store->getDataStore(),
                            new ChangeCookieCategory('997637a7-595e-4242-8ccd-7dc1476aeb63'),
                            new CreateNewCookieWithNotAcceptedCategory('997637a7-595e-4242-8ccd-7dc1476aeb63'),
                            new IgnoreUntilNexOccurrence(),
                            new IgnorePermanently(),
                        ),
                    )
                ],
            ),
            new ProblematicCookieSuggestion(
                '42c660ce-6770-4002-abd8-338a4015444d',
                'Problematic_two_categories',
                'example.com',
                [CookieOccurrence::fromCookieOccurrenceForResolving($problematicTwoCategoriesOccurrence2), CookieOccurrence::fromCookieOccurrenceForResolving($problematicTwoCategoriesOccurrence1)],
                [],
                ExistingCookie::fromCookieDataForSuggestion($problematicTwoCategoriesCookie1),
                [
                    new CookieIsInCategoryThatIsNotAcceptedByScenario(
                        'categoryC',
                        ['categoryA', 'categoryB', 'categoryD'],
                        CookieOccurrence::fromCookieOccurrenceForResolving($problematicTwoCategoriesOccurrence2),
                        new Solutions(
                            [$projectId, '42c660ce-6770-4002-abd8-338a4015444d', 'ac2cc255-8056-455f-92e8-716c6e17814e', '561109e2-5944-44ae-aedb-dcbc58e62d55', 'ProblematicCookieSuggestion', CookieIsInCategoryThatIsNotAcceptedByScenario::TYPE],
                            $store->getDataStore(),
                            new ChangeCookieCategory('ac2cc255-8056-455f-92e8-716c6e17814e'),
                            new CreateNewCookieWithNotAcceptedCategory('ac2cc255-8056-455f-92e8-716c6e17814e'),
                            new IgnoreUntilNexOccurrence(),
                            new IgnorePermanently(),
                        ),
                    )
                ],
            ),
            new ProblematicCookieSuggestion(
                '42c660ce-6770-4002-abd8-338a4015444d',
                'Problematic_two_categories',
                'example.com',
                [CookieOccurrence::fromCookieOccurrenceForResolving($problematicTwoCategoriesOccurrence2), CookieOccurrence::fromCookieOccurrenceForResolving($problematicTwoCategoriesOccurrence1)],
                [],
                ExistingCookie::fromCookieDataForSuggestion($problematicTwoCategoriesCookie2),
                [
                    new CookieIsInCategoryThatIsNotAcceptedByScenario(
                        'categoryD',
                        ['categoryA', 'categoryB', 'categoryC'],
                        CookieOccurrence::fromCookieOccurrenceForResolving($problematicTwoCategoriesOccurrence1),
                        new Solutions(
                            [$projectId, '42c660ce-6770-4002-abd8-338a4015444d', 'b9e3920f-9d67-4dd6-bd9b-1d3ce244a60c', 'babc3c1a-71cc-4776-a70e-b9ac4f4ed16c', 'ProblematicCookieSuggestion', CookieIsInCategoryThatIsNotAcceptedByScenario::TYPE],
                            $store->getDataStore(),
                            new ChangeCookieCategory('b9e3920f-9d67-4dd6-bd9b-1d3ce244a60c'),
                            new CreateNewCookieWithNotAcceptedCategory('b9e3920f-9d67-4dd6-bd9b-1d3ce244a60c'),
                            new IgnoreUntilNexOccurrence(),
                            new IgnorePermanently(),
                        ),
                    )
                ],
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
        );
    }

    private function createQueryBus(array $queriesPairedWithResults) {
        $queryBus = Mockery::mock(QueryBusInterface::class);

        foreach ($queriesPairedWithResults as [$query, $result]) {
            $queryBus->expects('dispatch')
                ->once()
                ->with($query)
                ->with(IsEqual::equalTo($query))
                ->andReturn($result);
        }

        return $queryBus;
    }

    private function createCommandBus(array $commands) {
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
            false,
            false,
            false,
            'Lax',
            $foundOnUrl,
        );
    }
}

(new CookieSuggestionStoreTest())->run();
