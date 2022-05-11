<?php

declare(strict_types=1);

namespace App\Domain\Project\Exception;

use DomainException;
use App\Domain\Project\ValueObject\ProjectId;

final class ProjectNotFoundException extends DomainException
{
	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $id
	 *
	 * @return static
	 */
	public static function withId(ProjectId $id): self
	{
		return new self(sprintf(
			'Project with ID %s not found.',
			$id
		));
	}
}
