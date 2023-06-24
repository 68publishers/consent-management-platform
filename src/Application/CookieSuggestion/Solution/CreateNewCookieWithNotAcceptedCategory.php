<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\Solution;

final class CreateNewCookieWithNotAcceptedCategory implements SolutionInterface
{
	private string $existingCookieId;

	public function __construct(
		string $existingCookieId
	) {
		$this->existingCookieId = $existingCookieId;
	}

	public function getType(): string
	{
		return 'create_new_cookie_with_not_accepted_category';
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
