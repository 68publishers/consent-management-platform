<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class ProjectCookieTotalsView extends AbstractView
{
	public int $providers;

	public int $commonCookies;

	public int $privateCookies;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'providers' => $this->providers,
			'commonCookies' => $this->commonCookies,
			'privateCookies' => $this->privateCookies,
		];
	}
}
