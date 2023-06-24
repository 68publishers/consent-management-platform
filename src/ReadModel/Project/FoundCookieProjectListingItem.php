<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

final class FoundCookieProjectListingItem
{
	public string $id;

	public string $name;

	public string $code;

	public string $color;

	public int $foundCookies;

	public function __construct(
		string $id,
		string $name,
		string $code,
		string $color,
		int $foundCookies
	) {
		$this->id = $id;
		$this->name = $name;
		$this->code = $code;
		$this->color = $color;
		$this->foundCookies = $foundCookies;
	}
}
