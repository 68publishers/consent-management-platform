<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\ArrayViewData;

final class ConsentStatisticsView extends AbstractView
{
	public ProjectId $projectId;

	public int $totalConsentsCount;

	public int $uniqueConsentsCount;

	public int $totalPositiveCount;

	public int $uniquePositiveCount;

	public int $totalNegativeCount;

	public int $uniqueNegativeCount;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 *
	 * @return static
	 */
	public static function createEmpty(ProjectId $projectId): self
	{
		return self::fromData(new ArrayViewData([
			'projectId' => $projectId,
			'totalConsentsCount' => 0,
			'uniqueConsentsCount' => 0,
			'totalPositiveCount' => 0,
			'uniquePositiveCount' => 0,
			'totalNegativeCount' => 0,
			'uniqueNegativeCount' => 0,
		]));
	}

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'projectId' => $this->projectId->toString(),
			'totalConsentsCount' => $this->totalConsentsCount,
			'uniqueConsentsCount' => $this->uniqueConsentsCount,
			'totalPositiveCount' => $this->totalPositiveCount,
			'uniquePositiveCount' => $this->uniquePositiveCount,
			'totalNegativeCount' => $this->totalNegativeCount,
			'uniqueNegativeCount' => $this->uniqueNegativeCount,
		];
	}
}
