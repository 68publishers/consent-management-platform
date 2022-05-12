<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings;

use App\Domain\ConsentSettings\ValueObject\ConsentSettingsId;

interface ConsentSettingsRepositoryInterface
{
	/**
	 * @param \App\Domain\ConsentSettings\ConsentSettings $consentSettings
	 *
	 * @return void
	 */
	public function save(ConsentSettings $consentSettings): void;

	/**
	 * @param \App\Domain\ConsentSettings\ValueObject\ConsentSettingsId $id
	 *
	 * @return \App\Domain\ConsentSettings\ConsentSettings
	 * @throws \App\Domain\ConsentSettings\Exception\ConsentSettingsNotFoundException
	 */
	public function get(ConsentSettingsId $id): ConsentSettings;
}
