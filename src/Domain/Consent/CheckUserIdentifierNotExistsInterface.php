<?php

declare(strict_types=1);

namespace App\Domain\Consent;

use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Consent\ValueObject\UserIdentifier;

interface CheckUserIdentifierNotExistsInterface
{
	/**
	 * @param \App\Domain\Consent\ValueObject\UserIdentifier $userIdentifier
	 * @param \App\Domain\Project\ValueObject\ProjectId      $projectId
	 *
	 * @return void
	 * @throws \App\Domain\Consent\Exception\UserIdentifierExistsException
	 */
	public function __invoke(UserIdentifier $userIdentifier, ProjectId $projectId): void;
}
