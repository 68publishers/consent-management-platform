<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings;

use App\Domain\Shared\ValueObject\Checksum;
use App\Domain\Project\ValueObject\ProjectId;

interface CheckChecksumNotExistsInterface
{
	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 * @param \App\Domain\Shared\ValueObject\Checksum   $checksum
	 *
	 * @return void
	 */
	public function __invoke(ProjectId $projectId, Checksum $checksum): void;
}
