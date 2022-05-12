<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings\Exception;

use DomainException;
use App\Domain\ConsentSettings\ValueObject\ConsentSettingsId;

final class ConsentSettingsNotFoundException extends DomainException
{
	/**
	 * @param \App\Domain\ConsentSettings\ValueObject\ConsentSettingsId $id
	 *
	 * @return static
	 */
	public static function withId(ConsentSettingsId $id): self
	{
		return new self(sprintf(
			'Consent settings with ID %s not found.',
			$id
		));
	}
}
