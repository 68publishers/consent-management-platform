<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class ConsentTotalsView extends AbstractView
{
	public ProjectId $projectId;

	public int $total;

	public int $unique;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'project_id' => $this->projectId->toString(),
			'total' => $this->total,
			'unique' => $this->unique,
		];
	}
}
