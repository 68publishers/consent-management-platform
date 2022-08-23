<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use DateTimeImmutable;
use DateTimeInterface;
use App\Domain\Shared\ValueObject\Checksum;
use App\Domain\Consent\ValueObject\ConsentId;
use App\Domain\Consent\ValueObject\UserIdentifier;
use App\Domain\ConsentSettings\ValueObject\ShortIdentifier;
use App\Domain\ConsentSettings\ValueObject\ConsentSettingsId;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class ConsentListView extends AbstractView
{
	public ConsentId $id;

	public DateTimeImmutable $createdAt;

	public DateTimeImmutable $lastUpdateAt;

	public UserIdentifier $userIdentifier;

	public ?Checksum $settingsChecksum = NULL;

	public ?ShortIdentifier $settingsShortIdentifier = NULL;

	public ?ConsentSettingsId $settingsId = NULL;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id->toString(),
			'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
			'lastUpdateAt' => $this->lastUpdateAt->format(DateTimeInterface::ATOM),
			'userIdentifier' => $this->userIdentifier->value(),
			'settingsChecksum' => NULL !== $this->settingsChecksum ? $this->settingsChecksum->value() : NULL,
			'shortIdentifier' => NULL !== $this->settingsShortIdentifier ? $this->settingsShortIdentifier->value() : NULL,
			'settingsId' => NULL !== $this->settingsId ? $this->settingsId->toString() : NULL,
		];
	}
}
