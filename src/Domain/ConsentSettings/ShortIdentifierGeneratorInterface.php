<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings;

use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\ConsentSettings\ValueObject\ShortIdentifier;

interface ShortIdentifierGeneratorInterface
{
	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 *
	 * @return \App\Domain\ConsentSettings\ValueObject\ShortIdentifier
	 * @throws \App\Domain\ConsentSettings\Exception\ShortIdentifierGeneratorException
	 */
	public function generate(ProjectId $projectId): ShortIdentifier;
}
