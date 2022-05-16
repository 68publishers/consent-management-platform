<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use DateTimeImmutable;
use DateTimeInterface;
use App\Domain\Shared\ValueObject\Checksum;
use App\Domain\Consent\ValueObject\Consents;
use App\Domain\Consent\ValueObject\ConsentId;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Consent\ValueObject\Attributes;
use App\Domain\Consent\ValueObject\UserIdentifier;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class ConsentView extends AbstractView
{
	public ConsentId $id;

	public ProjectId $projectId;

	public DateTimeImmutable $createdAt;

	public DateTimeImmutable $lastUpdateAt;

	public UserIdentifier $userIdentifier;

	public ?Checksum $settingsChecksum = NULL;

	public Consents $consents;

	public Attributes $attributes;

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
			'user_identifier' => $this->userIdentifier->value(),
			'settings_checksum' => NULL !== $this->settingsChecksum ? $this->settingsChecksum->value() : NULL,
			'consents' => $this->consents->values(),
			'attributes' => $this->attributes->values(),
		];
	}
}
