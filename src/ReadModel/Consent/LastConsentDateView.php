<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use DateTimeImmutable;
use DateTimeInterface;
use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class LastConsentDateView extends AbstractView
{
	public ProjectId $projectId;

	public ?DateTimeImmutable $lastConsentDate = NULL;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'projectId' => $this->projectId->toString(),
			'lastConsentDate' => NULL !== $this->lastConsentDate ? $this->lastConsentDate->format(DateTimeInterface::ATOM) : NULL,
		];
	}
}
