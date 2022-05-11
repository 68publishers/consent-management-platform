<?php

declare(strict_types=1);

namespace App\Domain\Project;

use App\Domain\Project\ValueObject\Code;
use App\Domain\Project\ValueObject\ProjectId;

interface CheckCodeUniquenessInterface
{
	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 * @param \App\Domain\Project\ValueObject\Code      $code
	 *
	 * @return void
	 * @throws \App\Domain\Project\Exception\CodeUniquenessException
	 */
	public function __invoke(ProjectId $projectId, Code $code): void;
}
