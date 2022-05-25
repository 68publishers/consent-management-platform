<?php

declare(strict_types=1);

namespace App\ReadModel\GlobalSettings;

use DateTimeImmutable;
use DateTimeInterface;
use App\Domain\Shared\ValueObject\Locales;
use App\Domain\GlobalSettings\ValueObject\GlobalSettingsId;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class GlobalSettingsView extends AbstractView
{
	public GlobalSettingsId $id;

	public DateTimeImmutable $createdAt;

	public DateTimeImmutable $lastUpdateAt;

	public Locales $locales;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id->toString(),
			'created_at' => $this->createdAt->format(DateTimeInterface::ATOM),
			'last_update_at' => $this->lastUpdateAt->format(DateTimeInterface::ATOM),
			'locales' => $this->locales->toArray(),
		];
	}
}
