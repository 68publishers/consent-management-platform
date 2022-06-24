<?php

declare(strict_types=1);

namespace App\Infrastructure\Project;

use App\Domain\Shared\ValueObject\Locale;
use App\Application\Cookie\CompileException;
use App\Domain\Project\ValueObject\Template;
use App\Application\Cookie\TemplateArguments;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Project\TemplateValidatorInterface;
use App\Application\Cookie\TemplateRendererInterface;
use App\Application\Cookie\Template as RenderableTemplate;
use App\Domain\Project\Exception\InvalidTemplateException;

final class TemplateValidator implements TemplateValidatorInterface
{
	private TemplateRendererInterface $cookieTemplateRenderer;

	/**
	 * @param \App\Application\Cookie\TemplateRendererInterface $cookieTemplateRenderer
	 */
	public function __construct(TemplateRendererInterface $cookieTemplateRenderer)
	{
		$this->cookieTemplateRenderer = $cookieTemplateRenderer;
	}

	/**
	 * {@inheritDoc}
	 */
	public function __invoke(ProjectId $projectId, Template $template, Locale $locale): void
	{
		$tpl = RenderableTemplate::create(
			$projectId->toString(),
			$template->value(),
			TemplateArguments::create([], [])
		);

		$tpl = $tpl->withOption(RenderableTemplate::OPTION_NO_CACHE, TRUE);

		try {
			$this->cookieTemplateRenderer->render($tpl);
		} catch (CompileException $e) {
			throw InvalidTemplateException::fromPrevious($projectId, $locale, $e);
		}
	}
}
