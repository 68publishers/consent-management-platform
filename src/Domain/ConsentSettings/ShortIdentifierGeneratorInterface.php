<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings;

use App\Domain\ConsentSettings\ValueObject\ShortIdentifier;

interface ShortIdentifierGeneratorInterface
{
	/**
	 * @return \App\Domain\ConsentSettings\ValueObject\ShortIdentifier
	 * @throws \App\Domain\ConsentSettings\Exception\ShortIdentifierGeneratorException
	 */
	public function generate(): ShortIdentifier;
}
