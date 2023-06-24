<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

final class CookieDataForSuggestion
{
	public string $id;

	public string $name;

	public string $domain;

	public string $categoryId;

	public string $categoryCode;

	public string $providerId;

	public string $providerCode;

	public string $providerName;

	public bool $associated;

	public function __construct(
		string $id,
		string $name,
		string $domain,
		string $categoryId,
		string $categoryCode,
		string $providerId,
		string $providerCode,
		string $providerName,
		bool $associated
	) {
		$this->id = $id;
		$this->name = $name;
		$this->domain = $domain;
		$this->categoryId = $categoryId;
		$this->categoryCode = $categoryCode;
		$this->providerId = $providerId;
		$this->providerCode = $providerCode;
		$this->providerName = $providerName;
		$this->associated = $associated;
	}
}
