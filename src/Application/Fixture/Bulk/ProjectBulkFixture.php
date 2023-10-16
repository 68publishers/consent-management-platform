<?php

declare(strict_types=1);

namespace App\Application\Fixture\Bulk;

use App\Application\Fixture\AbstractFixture;
use App\Domain\Category\Category;
use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Cookie\ValueObject\ProcessingTime;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\User\User;
use Behat\Transliterator\Transliterator;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use RuntimeException;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;

final class ProjectBulkFixture extends AbstractFixture
{
    protected function loadFixtures(ObjectManager $manager): array
    {
        assert($manager instanceof EntityManagerInterface);

        $fakerEn = FakerFactory::create('en_US');
        $fakerCs = FakerFactory::create('cs_CZ');

        $categoryIds = $this->getCategoryIds($manager);
        $userIds = $this->getUserIds($manager);

        if (empty($categoryIds)) {
            throw new RuntimeException('No categories found.');
        }

        $cookieProviders = $projectCookieProviders = $projects = $userHasProjects = $cookies = $consents = $consentSettings = [];

        # create 100 third party cookie providers
        for ($i = 0; $i < 100; $i++) {
            $cookieProviders[] = $this->createThirdPartyCookieProvider($fakerEn, $fakerCs);
        }

        # create 40 projects with first party providers
        for ($i = 0; $i < 40; $i++) {
            $projectName = $fakerEn->company() . $fakerEn->companySuffix();
            $projectCode = Transliterator::transliterate($projectName);
            $projectCookieProviders[] = $projectCookieProvider = $this->createFirstPartyCookieProvider($fakerEn, $fakerCs, $projectName, $projectCode);

            $projects[] = [
                'project_id' => ProjectId::new()->toString(),
                'name' => $projectName,
                'code' => $projectCode,
                'domain' => $fakerEn->domainName(),
                'description' => $fakerEn->realText(300),
                'color' => $fakerEn->hexColor(),
                'active' => true,
                'locales' => ['en', 'cs'],
                'default_locale' => 'en',
                'environments' => [],
                'cookie_provider_id' => $projectCookieProvider['cookie_provider_id'],
                'cookie_provider_ids' => array_map(
                    static fn (array $cpRow): string => $cpRow['cookie_provider_id'],
                    $fakerEn->randomElements($cookieProviders, $fakerEn->numberBetween(15, 50)),
                ),
            ];
        }

        # associate all projects with all users
        foreach ($userIds as $userId) {
            $userHasProjects[] = [
                'user_id' => $userId,
                'project_ids' => array_map(
                    static fn (array $projectRow): string => $projectRow['project_id'],
                    $projects,
                ),
            ];
        }

        # create 4 - 6 cookies per provider
        foreach (array_merge($projectCookieProviders, $cookieProviders) as $cookieProvider) {
            for ($i = 0; $i < $fakerEn->numberBetween(2, 6); $i++) {
                $cookies[] = $this->createCookie($fakerEn, $fakerCs, $fakerEn->randomElement($categoryIds), $cookieProvider['cookie_provider_id'], $cookieProvider['code']);
            }
        }

        # per each project create consent settings and 30 - 60 new consents and 15 - 30 updates
        foreach ($projects as $project) {
            $consentSettings[] = $currentConsentSettings = $this->createConsentSettings($fakerEn, $project['project_id']);

            for ($i = 0; $i < $fakerEn->numberBetween(200, 400); $i++) {
                $consents[] = $consent = $this->createConsent($fakerEn, $project['project_id'], $currentConsentSettings['checksum']);

                # update consent
                if (0 === $i % 4) {
                    $consent['consents'] = $this->createConsentsData($fakerEn);
                    $consents[] = $consent;
                }
            }
        }

        return [
            'project' => $projects,
            'cookie_provider' => array_merge($projectCookieProviders, $cookieProviders),
            'user_has_projects' => $userHasProjects,
            'cookie' => $cookies,
            'consent_settings' => $consentSettings,
            'consent' => $consents,
        ];
    }

    private function createThirdPartyCookieProvider(Faker $fakerEn, Faker $fakerCs): array
    {
        $name = $fakerEn->company() . $fakerEn->companySuffix();
        $code = Transliterator::transliterate($name);

        return [
            'cookie_provider_id' => CookieProviderId::new()->toString(),
            'code' => $code,
            'type' => ProviderType::THIRD_PARTY,
            'name' => $name,
            'link' => 'https://www.' . $code . '.com',
            'purposes' => [
                'cs' => $fakerCs->realText(150),
                'en' => $fakerEn->realText(150),
            ],
            'active' => true,
            'private' => false,
        ];
    }

    private function createFirstPartyCookieProvider(Faker $fakerEn, Faker $fakerCs, string $name, string $code): array
    {
        return [
            'cookie_provider_id' => CookieProviderId::new()->toString(),
            'code' => $code,
            'type' => ProviderType::FIRST_PARTY,
            'name' => $name,
            'link' => 'https://www.' . $code . '.com',
            'purposes' => [
                'cs' => $fakerCs->realText(150),
                'en' => $fakerEn->realText(150),
            ],
            'active' => true,
            'private' => true,
        ];
    }

    private function createCookie(Faker $fakerEn, Faker $fakerCs, string $categoryId, string $cookieProviderId, string $code): array
    {
        $processingTime = match ($fakerEn->numberBetween(1, 3)) {
            1 => ProcessingTime::SESSION,
            2 => ProcessingTime::PERSISTENT,
            default => sprintf('%sd', $fakerEn->numberBetween(30, 365)),
        };

        return [
            'cookie_id' => CookieId::new()->toString(),
            'category_id' => $categoryId,
            'cookie_provider_id' => $cookieProviderId,
            'name' => '__' . $fakerEn->lexify('?????'),
            'domain' => $code . '.com',
            'processing_time' => $processingTime,
            'active' => true,
            'purposes' => [
                'cs' => $fakerCs->realText(150),
                'en' => $fakerEn->realText(150),
            ],
            'environments' => true,
        ];
    }

    private function createConsentSettings(Faker $faker, string $projectId): array
    {
        return [
            'project_id' => $projectId,
            'checksum' => $checksum = $faker->sha256(),
            'settings' => [
                'fixture_consent_settings' => [
                    'project' => $projectId,
                    'checksum' => $checksum,
                ],
            ],
        ];
    }

    private function createConsent(Faker $faker, string $projectId, string $checksum): array
    {
        return [
            'project_id' => $projectId,
            'user_identifier' => $faker->uuid(),
            'settings_checksum' => $checksum,
            'consents' => $this->createConsentsData($faker),
            'attributes' => [
                'trackingId' => $faker->numerify('track-##########'),
                'email' => $faker->email(),
            ],
            'environment' => 'default',
        ];
    }

    private function createConsentsData(Faker $faker): array
    {
        return [
            'functionality_storage' => true,
            'personalization_storage' => $faker->boolean(),
            'security_storage' => $faker->boolean(),
            'ad_storage' => $faker->boolean(),
            'analytics_storage' => $faker->boolean(),
        ];
    }

    /**
     * @return array<string>
     */
    private function getCategoryIds(EntityManagerInterface $em): array
    {
        $rows = $em->createQueryBuilder()
            ->select('c.id')
            ->from(Category::class, 'c')
            ->where('c.deletedAt IS NULL')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);

        return array_map(static fn (CategoryId $categoryId): string => $categoryId->toString(), array_column($rows, 'id'));
    }

    /**
     * @return array<string>
     */
    private function getUserIds(EntityManagerInterface $em): array
    {
        $rows = $em->createQueryBuilder()
            ->select('u.id')
            ->from(User::class, 'u')
            ->where('u.deletedAt IS NULL')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);

        return array_map(static fn (UserId $userId): string => $userId->toString(), array_column($rows, 'id'));
    }
}
