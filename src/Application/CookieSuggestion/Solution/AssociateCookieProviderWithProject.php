<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Solution;

final class AssociateCookieProviderWithProject implements SolutionInterface
{
	private string $providerId;

	private string $providerName;

	public function __construct(
		string $providerId,
		string $providerName
	) {
		$this->providerId = $providerId;
		$this->providerName = $providerName;
	}

	public function getType(): string
	{
		return 'associate_cookie_provider_with_project';
	}

	public function getUniqueId(): string
	{
		return md5($this->getType() . '__x__' . $this->providerId);
	}

	public function getArguments(): array
	{
		return [
			'provider_id' => $this->providerId,
		];
	}

	public function getTranslatorArgs(): array
	{
		return [
			'provider_name' => $this->providerName,
		];
	}
}
