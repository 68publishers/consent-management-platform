<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use DateTimeImmutable;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns `ProjectCookieTotalsView`
 */
final class CalculateProjectCookieTotalsQuery extends AbstractQuery
{
    public static function create(string $projectId, DateTimeImmutable $maxDate): self
    {
        return self::fromParameters([
            'project_id' => $projectId,
            'max_date' => $maxDate,
        ]);
    }

    public function projectId(): string
    {
        return $this->getParam('project_id');
    }

    public function maxDate(): DateTimeImmutable
    {
        return $this->getParam('max_date');
    }

    public function namedEnvironment(): ?string
    {
        return $this->getParam('named_environment');
    }

    public function defaultEnvironment(): bool
    {
        return $this->getParam('default_environment') ?? false;
    }

    public function withNamedEnvironment(string $environment): self
    {
        return $this->withParam('named_environment', $environment)
            ->withParam('default_environment', false);
    }

    public function withDefaultEnvironment(): self
    {
        return $this->withParam('default_environment', true)
            ->withParam('named_environment', null);
    }
}
