<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

use App\Application\CookieSuggestion\Warning\CookieDoesNotHaveSameDomain;
use App\Application\CookieSuggestion\Warning\WarningInterface;
use App\ReadModel\Cookie\CookieDataForSuggestion;

final class ExistingCookie
{
    public string $cookieId;

    public string $cookieName;

    public string $cookieDomain;

    public string $categoryId;

    public string $categoryCode;

    public string $providerId;

    public string $providerCode;

    public string $providerName;

    /** @var array<int, WarningInterface> */
    public array $warnings = [];

    /**
     * @param array<int, WarningInterface> $warnings
     */
    public function __construct(
        string $cookieId,
        string $cookieName,
        string $cookieDomain,
        string $categoryId,
        string $categoryCode,
        string $providerId,
        string $providerCode,
        string $providerName,
        array $warnings,
    ) {
        $this->cookieId = $cookieId;
        $this->cookieName = $cookieName;
        $this->cookieDomain = $cookieDomain;
        $this->categoryId = $categoryId;
        $this->categoryCode = $categoryCode;
        $this->providerId = $providerId;
        $this->providerCode = $providerCode;
        $this->providerName = $providerName;
        $this->warnings = $warnings;
    }

    /**
     * @param array<int, WarningInterface> $additionalWarnings
     */
    public static function fromCookieDataForSuggestion(CookieDataForSuggestion $cookieDataForSuggestion, array $additionalWarnings = []): self
    {
        $warnings = [];

        if (!($cookieDataForSuggestion->getMetadataField($cookieDataForSuggestion::METADATA_FIELD_SAME_DOMAIN) ?? false)) {
            $warnings[] = new CookieDoesNotHaveSameDomain();
        }

        return new self(
            $cookieDataForSuggestion->id,
            $cookieDataForSuggestion->name,
            $cookieDataForSuggestion->domain ?: $cookieDataForSuggestion->projectDomain,
            $cookieDataForSuggestion->categoryId,
            $cookieDataForSuggestion->categoryCode,
            $cookieDataForSuggestion->providerId,
            $cookieDataForSuggestion->providerCode,
            $cookieDataForSuggestion->providerName,
            array_merge($warnings, $additionalWarnings),
        );
    }
}
