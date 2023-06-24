<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Solution;

final class ChangeCookieCategory implements SolutionInterface
{
	private string $existingCookieId;

	public function __construct(
		string $existingCookieId
	) {
		$this->existingCookieId = $existingCookieId;
	}

	public function getType(): string
	{
		return 'change_cookie_category';
	}

	public function getUniqueId(): string
	{
		return md5($this->getType());
	}

	public function getArguments(): array
	{
		return [
			'existing_cookie_id' => $this->existingCookieId,
		];
	}
}
