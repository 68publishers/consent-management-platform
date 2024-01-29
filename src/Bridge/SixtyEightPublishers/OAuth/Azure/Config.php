<?php

declare(strict_types=1);

namespace App\Bridge\SixtyEightPublishers\OAuth\Azure;

use App\Application\GlobalSettings\GlobalSettingsInterface;
use SixtyEightPublishers\OAuth\Authorization\Azure\AzureAuthorizator;
use SixtyEightPublishers\OAuth\Config\Config as SimpleConfig;
use SixtyEightPublishers\OAuth\Config\LazyConfig;

final class Config extends LazyConfig
{
    public function __construct(GlobalSettingsInterface $globalSettings)
    {
        parent::__construct(
            configFactory: static function () use ($globalSettings): SimpleConfig {
                $azureAuthSetting = $globalSettings->azureAuthSettings();

                return new SimpleConfig(
                    flowEnabled: $azureAuthSetting->enabled(),
                    options: [
                        AzureAuthorizator::OptClientId => $azureAuthSetting->clientId(),
                        AzureAuthorizator::OptClientSecret => $azureAuthSetting->clientSecret(),
                        AzureAuthorizator::OptTenantId => $azureAuthSetting->tenantId(),
                    ],
                );
            },
        );
    }
}
