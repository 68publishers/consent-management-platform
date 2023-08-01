<?php

declare(strict_types=1);

namespace App\Domain\Consent;

use App\Domain\Consent\Exception\UserIdentifierExistsException;
use App\Domain\Consent\ValueObject\UserIdentifier;
use App\Domain\Project\ValueObject\ProjectId;

interface CheckUserIdentifierNotExistsInterface
{
    /**
     * @throws UserIdentifierExistsException
     */
    public function __invoke(UserIdentifier $userIdentifier, ProjectId $projectId): void;
}
