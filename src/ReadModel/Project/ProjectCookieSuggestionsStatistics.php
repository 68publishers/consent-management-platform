<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use DateTimeImmutable;

final class ProjectCookieSuggestionsStatistics
{
	public int $missing;

	public int $unassociated;

	public int $problematic;

	public int $unproblematic;

	public int $ignored;

	public int $total;

	public int $totalWithoutVirtual;

	public ?DateTimeImmutable $latestFoundAt;

	public function __construct(
		int $missing,
		int $unassociated,
		int $problematic,
		int $unproblematic,
		int $ignored,
		int $total,
		int $totalWithoutVirtual,
		?DateTimeImmutable $latestFoundAt
	) {
		$this->missing = $missing;
		$this->unassociated = $unassociated;
		$this->problematic = $problematic;
		$this->unproblematic = $unproblematic;
		$this->ignored = $ignored;
		$this->total = $total;
		$this->totalWithoutVirtual = $totalWithoutVirtual;
		$this->latestFoundAt = $latestFoundAt;
	}
}
