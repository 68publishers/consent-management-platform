<?php

declare(strict_types=1);

namespace App\Domain\Project\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class AddCookieProvidersToProjectCommand extends AbstractCommand
{
    public static function create(string $projectId, string $cookieProviderId, string ...$cookieProviderIds): self
    {
        return self::fromParameters([
            'project_id' => $projectId,
            'cookie_provider_ids' => array_merge([$cookieProviderId], $cookieProviderIds),
        ]);
    }

    public function projectId(): string
    {
        return $this->getParam('project_id');
    }

    /**
     * @return array<string>
     */
    public function cookieProviderIds(): array
    {
        return $this->getParam('cookie_provider_ids');
    }
}
