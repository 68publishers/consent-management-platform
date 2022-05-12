<?php

declare(strict_types=1);

namespace App\ReadModel\ConsentSettings;

use App\Domain\Shared\ValueObject\Checksum;
use App\Domain\ConsentSettings\ValueObject\SettingsGroup;
use App\Domain\ConsentSettings\ValueObject\ConsentSettingsId;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class ConsentSettingsView extends AbstractView
{
	public ConsentSettingsId $id;

	public Checksum $checksum;

	public SettingsGroup $settings;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id->toString(),
			'checksum' => $this->checksum->value(),
			'settings' => $this->settings->toArray(),
		];
	}
}
