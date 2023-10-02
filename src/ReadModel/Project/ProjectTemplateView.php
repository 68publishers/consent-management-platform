<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use App\Domain\Project\ValueObject\Environments;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Project\ValueObject\Template;
use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Shared\ValueObject\LocalesConfig;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class ProjectTemplateView extends AbstractView
{
    public ProjectId $projectId;

    public Template $template;

    public Locale $templateLocale;

    public LocalesConfig $projectLocalesConfig;

    public Environments $environments;

    public function jsonSerialize(): array
    {
        return [
            'projectId' => $this->projectId->toString(),
            'template' => $this->template->value(),
            'locale' => $this->templateLocale->value(),
        ];
    }
}
