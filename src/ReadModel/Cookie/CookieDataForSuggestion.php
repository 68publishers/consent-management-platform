<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

final class CookieDataForSuggestion
{
    public const METADATA_FIELD_SAME_DOMAIN = 'same_domain';

    public string $id;

    public string $name;

    public string $domain;

    public string $projectDomain;

    public string $categoryId;

    public string $categoryCode;

    public string $providerId;

    public string $providerCode;

    public string $providerName;

    public bool $associated;

    /** @var array<string, mixed> */
    private array $metadata = [];

    public function __construct(
        string $id,
        string $name,
        string $domain,
        string $projectDomain,
        string $categoryId,
        string $categoryCode,
        string $providerId,
        string $providerCode,
        string $providerName,
        bool $associated,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->domain = $domain;
        $this->projectDomain = $projectDomain;
        $this->categoryId = $categoryId;
        $this->categoryCode = $categoryCode;
        $this->providerId = $providerId;
        $this->providerCode = $providerCode;
        $this->providerName = $providerName;
        $this->associated = $associated;
    }

    public function withMetadataField(string $key, $value): self
    {
        $cookie = clone $this;
        $cookie->metadata[$key] = $value;

        return $cookie;
    }

    public function getMetadataField(string $key)
    {
        return $this->metadata[$key] ?? null;
    }
}
