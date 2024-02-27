<?php

declare(strict_types=1);

use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Cookie\ValueObject\ProcessingTime;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\User\RolesEnum;
use Nette\Utils\Random;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;

$fixtures = [];

$fixtures['global_settings_localization'] = [
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
        'active' => true,
        'necessary' => true,
    ],
    'personalization_storage' => [
        'category_id' => CategoryId::new()->toString(),
        'code' => 'personalization_storage',
        'names' => [
            'cs' => 'Personalizační cookies',
            'en' => 'Personalization cookies',
        ],
        'active' => true,
        'necessary' => false,
    ],
    'security_storage' => [
        'category_id' => CategoryId::new()->toString(),
        'code' => 'security_storage',
        'names' => [
            'cs' => 'Bezpečnostní cookies',
            'en' => 'Security cookies',
        ],
        'active' => true,
        'necessary' => false,
    ],
    'ad_storage' => [
        'category_id' => CategoryId::new()->toString(),
        'code' => 'ad_storage',
        'names' => [
            'cs' => 'Reklamní cookies',
            'en' => 'Ad cookies',
        ],
        'active' => true,
        'necessary' => false,
    ],
    'ad_user_data' => [
        'category_id' => CategoryId::new()->toString(),
        'code' => 'ad_user_data',
        'names' => [
            'cs' => 'Ad user data',
            'en' => 'Ad user data',
        ],
        'active' => true,
        'necessary' => false,
    ],
    'ad_personalization' => [
        'category_id' => CategoryId::new()->toString(),
        'code' => 'ad_personalization',
        'names' => [
            'cs' => 'Ad personalization',
            'en' => 'Ad personalization',
        ],
        'active' => true,
        'necessary' => false,
    ],
    'analytics_storage' => [
        'category_id' => CategoryId::new()->toString(),
        'code' => 'analytics_storage',
        'names' => [
            'cs' => 'Analytické cookies',
            'en' => 'Analytics cookies',
        ],
        'active' => true,
        'necessary' => false,
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
        'active' => true,
        'private' => true,
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
        'active' => true,
        'private' => false,
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
        'active' => true,
        'private' => false,
    ],
];

$fixtures['cookie'] = [
    [
        'cookie_id' => CookieId::new()->toString(),
        'category_id' => $fixtures['category']['functionality_storage']['category_id'],
        'cookie_provider_id' => $fixtures['cookie_provider']['demo']['cookie_provider_id'],
        'name' => 'PHPSESSID',
        'domain' => '',
        'processing_time' => ProcessingTime::SESSION,
        'active' => true,
        'purposes' => [
            'cs' => 'Session ID zákazníka.',
            'en' => 'Customer\'s session ID.',
        ],
        'environments' => true,
    ],
    [
        'cookie_id' => CookieId::new()->toString(),
        'category_id' => $fixtures['category']['security_storage']['category_id'],
        'cookie_provider_id' => $fixtures['cookie_provider']['facebook_login']['cookie_provider_id'],
        'name' => 'c_user',
        'domain' => 'facebook.com',
        'processing_time' => ProcessingTime::SESSION,
        'active' => true,
        'purposes' => [
            'cs' => 'Facebook ID zákazníka.',
            'en' => 'Customer\'s Facebook ID.',
        ],
        'environments' => true,
    ],
    [
        'cookie_id' => CookieId::new()->toString(),
        'category_id' => $fixtures['category']['ad_storage']['category_id'],
        'cookie_provider_id' => $fixtures['cookie_provider']['google_ads']['cookie_provider_id'],
        'name' => '__gads',
        'domain' => 'google.com',
        'processing_time' => '13m',
        'active' => true,
        'purposes' => [
            'cs' => 'Reklama.',
            'en' => 'Advertising.',
        ],
        'environments' => true,
    ],
];

$fixtures['project'] = [
    'demo' => [
        'project_id' => ProjectId::new()->toString(),
        'name' => 'Demo',
        'code' => 'demo',
        'domain' => 'demo.io',
        'description' => 'The demo project.',
        'color' => '#DB2777',
        'active' => true,
        'locales' => ['en', 'cs'],
        'default_locale' => 'en',
        'environments' => [],
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
        'firstname' => 'SixtyEightPublishers',
        'surname' => 'Admin',
        'roles' => [RolesEnum::ADMIN],
        'project_ids' => [
            $fixtures['project']['demo']['project_id'],
        ],
        'profile' => 'cs',
        'timezone' => 'Europe/Prague',
    ],
    'manager' => [
        'user_id' => UserId::new()->toString(),
        'username' => 'manager@68publishers.io',
        'email_address' => 'manager@68publishers.io',
        'password' => 'manager',
        'firstname' => 'SixtyEightPublishers',
        'surname' => 'Manager',
        'roles' => [RolesEnum::MANAGER],
        'project_ids' => [
            $fixtures['project']['demo']['project_id'],
        ],
        'profile' => 'cs',
        'timezone' => 'Europe/Prague',
    ],
];

$fixtures['consent'] = [];

/**
 * @throws Exception
 */
$getRandomConsents = static function (): array {
    return [
        'functionality_storage' => true,
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
        'settings_checksum' => null,
        'consents' => $getRandomConsents(),
        'attributes' => [
            'trackingId' => Random::generate(10, '0-9'),
        ],
        'environment' => 'default',
    ];

    # update consent
    if (0 === $i % 2) {
        $args['consents'] = $getRandomConsents();
        $fixtures['consent'][] = $args;
    }
}

return $fixtures;
