<?php

declare(strict_types=1);

namespace App\ReadModel\CookieSuggestion;

final class CookieSuggestion
{
	public string $id;

	public string $projectId;

	public string $name;

	public string $domain;

	public bool $ignored;

	public function __construct(
		string $id,
		string $projectId,
		string $name,
		string $domain,
		bool $ignored
	) {
		$this->id = $id;
		$this->projectId = $projectId;
		$this->name = $name;
		$this->domain = $domain;
		$this->ignored = $ignored;
	}
}
