<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class ProjectCookieTotalsView extends AbstractView
{
	public ProjectId $projectId;

	public int $providers;

	public int $commonCookies;

	public int $privateCookies;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'projectId' => $this->projectId->toString(),
			'providers' => $this->providers,
			'commonCookies' => $this->commonCookies,
			'privateCookies' => $this->privateCookies,
		];
	}
}
