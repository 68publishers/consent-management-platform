<?php

declare(strict_types=1);

namespace App\Application\Crawler;

use App\Application\GlobalSettings\GlobalSettingsInterface;
use GuzzleHttp\RequestOptions;
use SixtyEightPublishers\CrawlerClient\Authentication\Credentials;
use SixtyEightPublishers\CrawlerClient\CrawlerClient;
use SixtyEightPublishers\CrawlerClient\CrawlerClientInterface;
use SixtyEightPublishers\CrawlerClient\Serializer\JmsSerializer;

final class CrawlerClientProvider
{
    private GlobalSettingsInterface $globalSettings;

    private string $cacheDir;

    private bool $debugMode;

    private ?CrawlerClientInterface $client = null;

    public function __construct(GlobalSettingsInterface $globalSettings, string $cacheDir, bool $debugMode)
    {
        $this->globalSettings = $globalSettings;
        $this->cacheDir = $cacheDir;
        $this->debugMode = $debugMode;
    }

    /**
     * @throws CrawlerNotConfiguredException
     */
    public function get(): CrawlerClientInterface
    {
        if (null !== $this->client) {
            return $this->client;
        }

        $crawlerSettings = $this->globalSettings->crawlerSettings();

        if (false === $crawlerSettings->enabled() || null === $crawlerSettings->hostUrl() || null === $crawlerSettings->username() || null === $crawlerSettings->password()) {
            throw new CrawlerNotConfiguredException('');
        }

        $client = CrawlerClient::create(rtrim($crawlerSettings->hostUrl(), '/'), [
            RequestOptions::TIMEOUT => 8,
        ]);

        $client = $client
            ->withSerializer(JmsSerializer::default($this->cacheDir, $this->debugMode))
            ->withAuthentication(new Credentials($crawlerSettings->username(), $crawlerSettings->password()));

        return $this->client = $client;
    }
}
