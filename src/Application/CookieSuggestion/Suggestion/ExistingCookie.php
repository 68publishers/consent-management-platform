<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

use App\Application\CookieSuggestion\Warning\CookieDoesNotHaveSameDomain;
use App\Application\CookieSuggestion\Warning\WarningInterface;
use App\ReadModel\Cookie\CookieDataForSuggestion;

final class ExistingCookie
{
    /**
     * @param array<int, WarningInterface> $warnings
     */
    public function __construct(
        public string $cookieId,
        public string $cookieName,
        public string $cookieDomain,
        public string $categoryId,
        public string $categoryCode,
        public string $providerId,
        public string $providerCode,
        public string $providerName,
        public array $warnings,
    ) {}

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
