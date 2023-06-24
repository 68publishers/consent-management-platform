<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Solution;

final class AssociateCookieProviderWithProject implements SolutionInterface
{
	private string $providerId;

	public function __construct(
		string $providerId
	) {
		$this->providerId = $providerId;
	}

	public function getType(): string
	{
		return 'associate_cookie_provider_with_project';
	}

	public function getUniqueId(): string
	{
		return md5($this->getType());
	}

	public function getArguments(): array
	{
		return [
			'provider_id' => $this->providerId,
		];
	}
}
