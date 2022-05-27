<?php

declare(strict_types=1);

namespace App\ReadModel\GlobalSettings;

use DateTimeImmutable;
use DateTimeInterface;
use App\Domain\Shared\ValueObject\LocalesConfig;
use App\Domain\GlobalSettings\ValueObject\GlobalSettingsId;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class GlobalSettingsView extends AbstractView
{
	public GlobalSettingsId $id;

	public DateTimeImmutable $createdAt;

	public DateTimeImmutable $lastUpdateAt;

	public LocalesConfig $locales;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id->toString(),
			'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
			'lastUpdateAt' => $this->lastUpdateAt->format(DateTimeInterface::ATOM),
			'locales' => $this->locales->locales()->toArray(),
			'defaultLocale' => $this->locales->defaultLocale()->value(),
		];
	}
}
