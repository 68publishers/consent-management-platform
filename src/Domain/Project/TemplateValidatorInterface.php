<?php

declare(strict_types=1);

namespace App\Domain\Project;

use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Project\ValueObject\Template;
use App\Domain\Project\ValueObject\ProjectId;

interface TemplateValidatorInterface
{
	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 * @param \App\Domain\Project\ValueObject\Template  $template
	 * @param \App\Domain\Shared\ValueObject\Locale     $locale
	 *
	 * @return void
	 * @throws \App\Domain\Project\Exception\InvalidTemplateException
	 */
	public function __invoke(ProjectId $projectId, Template $template, Locale $locale): void;
}
