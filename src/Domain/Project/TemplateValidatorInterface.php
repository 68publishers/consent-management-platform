<?php

declare(strict_types=1);

namespace App\Domain\Project;

use App\Domain\Project\Exception\InvalidTemplateException;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Project\ValueObject\Template;
use App\Domain\Shared\ValueObject\Locale;

interface TemplateValidatorInterface
{
    /**
     * @throws InvalidTemplateException
     */
    public function __invoke(ProjectId $projectId, Template $template, Locale $locale): void;
}
