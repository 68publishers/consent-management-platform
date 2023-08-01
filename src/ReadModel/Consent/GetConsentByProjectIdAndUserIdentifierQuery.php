<?php

declare(strict_types=1);

namespace App\ReadModel\Consent;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

final class GetConsentByProjectIdAndUserIdentifierQuery extends AbstractQuery
{
    public static function create(string $projectId, string $userIdentifier): self
    {
        return self::fromParameters([
            'project_id' => $projectId,
            'user_identifier' => $userIdentifier,
        ]);
    }

    public function projectId(): string
    {
        return $this->getParam('project_id');
    }

    public function userIdentifier(): string
    {
        return $this->getParam('user_identifier');
    }
}
