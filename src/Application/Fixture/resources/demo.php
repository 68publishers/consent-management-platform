<?php

declare(strict_types=1);

use Nette\Utils\Random;
use App\Domain\User\RolesEnum;
use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Cookie\ValueObject\ProcessingTime;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;

$fixtures = [];

$fixtures['global_settings'] = [
	[
		'locales' => ['en', 'cs', 'de'],
		'default_locale' => 'en',
	],
];

$fixtures['category'] = [
	'functionality_storage' => [
		'category_id' => CategoryId::new()->toString(),
		'code' => 'functionality_storage',
		'names' => [
			'cs' => 'Nezbytně nutné soubory cookies',
			'en' => 'Functionality cookies',
		],
		'active' => TRUE,
	],
	'personalization_storage' => [
		'category_id' => CategoryId::new()->toString(),
		'code' => 'personalization_storage',
		'names' => [
			'cs' => 'Personalizační cookies',
			'en' => 'Personalization cookies',
		],
		'active' => TRUE,
	],
	'security_storage' => [
		'category_id' => CategoryId::new()->toString(),
		'code' => 'security_storage',
		'names' => [
			'cs' => 'Bezpečnostní cookies',
			'en' => 'Security cookies',
		],
		'active' => TRUE,
	],
	'ad_storage' => [
		'category_id' => CategoryId::new()->toString(),
		'code' => 'ad_storage',
		'names' => [
			'cs' => 'Reklamní cookies',
			'en' => 'Ad cookies',
		],
		'active' => TRUE,
	],
	'analytics_storage' => [
		'category_id' => CategoryId::new()->toString(),
		'code' => 'analytics_storage',
		'names' => [
			'cs' => 'Analytické cookies',
			'en' => 'Analytics cookies',
		],
		'active' => TRUE,
	],
];

$fixtures['cookie_provider'] = [
	'demo' => [
		'cookie_provider_id' => CookieProviderId::new()->toString(),
		'code' => 'demo',
		'type' => ProviderType::FIRST_PARTY,
		'name' => 'Demo website',
		'link' => 'https://www.demo.io',
		'purposes' => [
			'cs' => 'Naše vlastní cookies, které jsou nutné pro provoz našeho webu.',
			'en' => 'Our own cookies that are necessary for the operation of our website.',
		],
		'private' => TRUE,
	],
	'facebook_login' => [
		'cookie_provider_id' => CookieProviderId::new()->toString(),
		'code' => 'facebook_login',
		'type' => ProviderType::THIRD_PARTY,
		'name' => 'Facebook Login',
		'link' => 'https://www.facebook.com/about/privacy/',
		'purposes' => [
			'cs' => 'Platforma pro přihlášení skrze Facebook.',
			'en' => 'Facebook login platform.',
		],
		'private' => FALSE,
	],
	'google_ads' => [
		'cookie_provider_id' => CookieProviderId::new()->toString(),
		'code' => 'google_ads',
		'type' => ProviderType::THIRD_PARTY,
		'name' => 'Google Ads',
		'link' => 'https://policies.google.com/privacy',
		'purposes' => [
			'cs' => 'Platforma pro reklamu, retargeting a měření konverzí.',
			'en' => 'The platform for advertising, retargeting, and conversion measurement.',
		],
		'private' => FALSE,
	],
];

$fixtures['cookie'] = [
	[
		'cookie_id' => CookieId::new()->toString(),
		'category_id' => $fixtures['category']['functionality_storage']['category_id'],
		'cookie_provider_id' => $fixtures['cookie_provider']['demo']['cookie_provider_id'],
		'name' => 'PHPSESSID',
		'processing_time' => ProcessingTime::SESSION,
		'purposes' => [
			'cs' => 'Session ID zákazníka.',
			'en' => 'Customer\'s session ID.',
		],
	],
	[
		'cookie_id' => CookieId::new()->toString(),
		'category_id' => $fixtures['category']['security_storage']['category_id'],
		'cookie_provider_id' => $fixtures['cookie_provider']['facebook_login']['cookie_provider_id'],
		'name' => 'c_user',
		'processing_time' => ProcessingTime::SESSION,
		'purposes' => [
			'cs' => 'Facebook ID zákazníka.',
			'en' => 'Customer\'s Facebook ID.',
		],
	],
	[
		'cookie_id' => CookieId::new()->toString(),
		'category_id' => $fixtures['category']['ad_storage']['category_id'],
		'cookie_provider_id' => $fixtures['cookie_provider']['google_ads']['cookie_provider_id'],
		'name' => '__gads',
		'processing_time' => '13m',
		'purposes' => [
			'cs' => 'Reklama.',
			'en' => 'Advertising.',
		],
	],
];

$fixtures['project'] = [
	'demo' => [
		'project_id' => ProjectId::new()->toString(),
		'name' => 'Demo',
		'code' => 'demo',
		'description' => 'The demo project.',
		'color' => '#DB2777',
		'active' => TRUE,
		'locales' => ['en', 'cs'],
		'default_locale' => 'en',
		'timezone' => 'Europe/Prague',
		'cookie_provider_id' => $fixtures['cookie_provider']['demo']['cookie_provider_id'],
		'cookie_provider_ids' => [
			$fixtures['cookie_provider']['facebook_login']['cookie_provider_id'],
			$fixtures['cookie_provider']['google_ads']['cookie_provider_id'],
		],
	],
];

$fixtures['user'] = [
	'admin' => [
		'user_id' => UserId::new()->toString(),
		'username' => 'admin@68publishers.io',
		'email_address' => 'admin@68publishers.io',
		'password' => 'admin',
		'firstname' => 'Admin',
		'surname' => 'SixtyEightPublishers',
		'roles' => [RolesEnum::ADMIN],
		'project_ids' => [
			$fixtures['project']['demo']['project_id'],
		],
		'profile' => 'cs',
		'timezone' => 'Europe/Prague',
	],
];

$fixtures['consent'] = [];

$getRandomConsents = static function (): array {
	return [
		'functionality_storage' => TRUE,
		'personalization_storage' => 50 >= random_int(1, 100),
		'security_storage' => 50 >= random_int(1, 100),
		'ad_storage' => 50 >= random_int(1, 100),
		'analytics_storage' => 50 >= random_int(1, 100),
	];
};

for ($i = 0; $i < 30; $i++) {
	# create consent
	$fixtures['consent'][] = $args = [
		'project_id' => $fixtures['project']['demo']['project_id'],
		'user_identifier' => Random::generate(15, '0-9a-zA-Z'),
		'settings_checksum' => NULL,
		'consents' => $getRandomConsents(),
		'attributes' => [
			'trackingId' => Random::generate(10, '0-9'),
		],
	];

	# update consent
	if (0 === $i % 2) {
		$args['consents'] = $getRandomConsents();
		$fixtures['consent'][] = $args;
	}
}

return $fixtures;
