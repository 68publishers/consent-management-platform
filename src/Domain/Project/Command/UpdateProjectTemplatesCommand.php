<?php

declare(strict_types=1);

namespace App\Domain\Project\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class UpdateProjectTemplatesCommand extends AbstractCommand
{
    public static function create(string $projectId): self
    {
        return self::fromParameters([
            'project_id' => $projectId,
            'templates' => [],
        ]);
    }

    public function withTemplate(string $locale, string $template): self
    {
        $templates = $this->templates();
        $templates[$locale] = $template;

        return $this->withParam('templates', $templates);
    }

    public function projectId(): string
    {
        return $this->getParam('project_id');
    }

    /**
     * @return array<string, string>
     */
    public function templates(): array
    {
        return $this->getParam('templates');
    }
}
