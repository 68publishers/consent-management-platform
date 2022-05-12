<?php

declare(strict_types=1);

namespace App\ReadModel\ConsentSettings;

use DateTimeImmutable;
use DateTimeInterface;
use App\Domain\Shared\ValueObject\Checksum;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\ConsentSettings\ValueObject\SettingsGroup;
use App\Domain\ConsentSettings\ValueObject\ConsentSettingsId;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class ConsentSettingsView extends AbstractView
{
	public ConsentSettingsId $id;

	public ProjectId $projectId;

	public DateTimeImmutable $createdAt;

	public DateTimeImmutable $lastUpdateAt;

	public Checksum $checksum;

	public SettingsGroup $settings;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id->toString(),
			'project_id' => $this->projectId->toString(),
			'created_at' => $this->createdAt->format(DateTimeInterface::ATOM),
			'last_update_at' => $this->lastUpdateAt->format(DateTimeInterface::ATOM),
			'checksum' => $this->checksum->value(),
			'settings' => $this->settings->toArray(),
		];
	}
}
