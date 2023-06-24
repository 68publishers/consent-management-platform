<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Suggestion;

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

	public function __construct(
		string $cookieId,
		string $cookieName,
		string $cookieDomain,
		string $categoryId,
		string $categoryCode,
		string $providerId,
		string $providerCode,
		string $providerName
	) {
		$this->cookieId = $cookieId;
		$this->cookieName = $cookieName;
		$this->cookieDomain = $cookieDomain;
		$this->categoryId = $categoryId;
		$this->categoryCode = $categoryCode;
		$this->providerId = $providerId;
		$this->providerCode = $providerCode;
		$this->providerName = $providerName;
	}

	public static function fromCookieDataForSuggestion(CookieDataForSuggestion $cookieDataForSuggestion): self
	{
		return new self(
			$cookieDataForSuggestion->id,
			$cookieDataForSuggestion->name,
			$cookieDataForSuggestion->domain,
			$cookieDataForSuggestion->categoryId,
			$cookieDataForSuggestion->categoryCode,
			$cookieDataForSuggestion->providerId,
			$cookieDataForSuggestion->providerCode,
			$cookieDataForSuggestion->providerName,
		);
	}
}
