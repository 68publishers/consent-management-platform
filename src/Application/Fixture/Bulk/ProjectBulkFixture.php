<?php

declare(strict_types=1);

namespace App\Application\Fixture\Bulk;

use RuntimeException;
use App\Domain\User\User;
use Faker\Generator as Faker;
use Doctrine\ORM\AbstractQuery;
use App\Domain\Category\Category;
use Faker\Factory as FakerFactory;
use Doctrine\Persistence\ObjectManager;
use Behat\Transliterator\Transliterator;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Cookie\ValueObject\CookieId;
use App\Application\Fixture\AbstractFixture;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Cookie\ValueObject\ProcessingTime;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;

final class ProjectBulkFixture extends AbstractFixture
{
	/**
	 * {@inheritDoc}
	 */
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
				'description' => $fakerEn->realText(300),
				'color' => $fakerEn->hexColor(),
				'active' => TRUE,
				'locales' => ['en', 'cs'],
				'default_locale' => 'en',
				'cookie_provider_id' => $projectCookieProvider['cookie_provider_id'],
				'cookie_provider_ids' => array_map(
					static fn (array $cpRow): string => $cpRow['cookie_provider_id'],
					$fakerEn->randomElements($cookieProviders, $fakerEn->numberBetween(15, 50), FALSE)
				),
			];
		}

		# associate all projects with all users
		foreach ($userIds as $userId) {
			$userHasProjects[] = [
				'user_id' => $userId,
				'project_ids' => array_map(
					static fn (array $projectRow): string => $projectRow['project_id'],
					$projects
				),
			];
		}

		# create 4 - 6 cookies per provider
		foreach (array_merge($projectCookieProviders, $cookieProviders) as $cookieProvider) {
			for ($i = 0; $i < $fakerEn->numberBetween(2, 6); $i++) {
				$cookies[] = $this->createCookie($fakerEn, $fakerCs, $fakerEn->randomElement($categoryIds), $cookieProvider['cookie_provider_id']);
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

	/**
	 * @param \Faker\Generator $fakerEn
	 * @param \Faker\Generator $fakerCs
	 *
	 * @return array
	 */
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
			'active' => TRUE,
			'private' => FALSE,
		];
	}

	/**
	 * @param \Faker\Generator $fakerEn
	 * @param \Faker\Generator $fakerCs
	 * @param string           $name
	 * @param string           $code
	 *
	 * @return array
	 */
	private function createFirstPartyCookieProvider(Faker $fakerEn, Faker $fakerCs, string $name, string $code): array
	{
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
			'active' => TRUE,
			'private' => TRUE,
		];
	}

	/**
	 * @param \Faker\Generator $fakerEn
	 * @param \Faker\Generator $fakerCs
	 * @param string           $categoryId
	 * @param string           $cookieProviderId
	 *
	 * @return array
	 */
	private function createCookie(Faker $fakerEn, Faker $fakerCs, string $categoryId, string $cookieProviderId): array
	{
		switch ($fakerEn->numberBetween(1, 3)) {
			case 1:
				$processingTime = ProcessingTime::SESSION;

				break;
			case 2:
				$processingTime = ProcessingTime::PERSISTENT;

				break;
			case 3:
			default:
				$processingTime = sprintf('%sd', $fakerEn->numberBetween(30, 365));
		}

		return [
			'cookie_id' => CookieId::new()->toString(),
			'category_id' => $categoryId,
			'cookie_provider_id' => $cookieProviderId,
			'name' => '__' . $fakerEn->lexify('?????'),
			'processing_time' => $processingTime,
			'active' => TRUE,
			'purposes' => [
				'cs' => $fakerCs->realText(150),
				'en' => $fakerEn->realText(150),
			],
		];
	}

	/**
	 * @param \Faker\Generator $faker
	 * @param string           $projectId
	 *
	 * @return array
	 */
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

	/**
	 * @param \Faker\Generator $faker
	 * @param string           $projectId
	 * @param string           $checksum
	 *
	 * @return array
	 */
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
		];
	}

	/**
	 * @param \Faker\Generator $faker
	 *
	 * @return array
	 */
	private function createConsentsData(Faker $faker): array
	{
		return [
			'functionality_storage' => TRUE,
			'personalization_storage' => $faker->boolean(),
			'security_storage' => $faker->boolean(),
			'ad_storage' => $faker->boolean(),
			'analytics_storage' => $faker->boolean(),
		];
	}

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface $em
	 *
	 * @return string[]
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
	 * @param \Doctrine\ORM\EntityManagerInterface $em
	 *
	 * @return string[]
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
