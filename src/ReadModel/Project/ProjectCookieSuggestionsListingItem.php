<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

final class ProjectCookieSuggestionsListingItem
{
	public string $id;

	public string $code;

	public string $name;

	public ProjectCookieSuggestionsStatistics $statistics;

	public function __construct(
		string $id,
		string $code,
		string $name,
		ProjectCookieSuggestionsStatistics $statistics
	) {
		$this->id = $id;
		$this->code = $code;
		$this->name = $name;
		$this->statistics = $statistics;
	}
}
