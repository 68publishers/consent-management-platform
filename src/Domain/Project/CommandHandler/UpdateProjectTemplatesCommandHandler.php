<?php

declare(strict_types=1);

namespace App\Domain\Project\CommandHandler;

use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Project\ValueObject\Template;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Project\ProjectRepositoryInterface;
use App\Domain\Project\TemplateValidatorInterface;
use App\Domain\Project\Command\UpdateProjectTemplatesCommand;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class UpdateProjectTemplatesCommandHandler implements CommandHandlerInterface
{
	private ProjectRepositoryInterface $projectRepository;

	private TemplateValidatorInterface $templateValidator;

	/**
	 * @param \App\Domain\Project\ProjectRepositoryInterface $projectRepository
	 * @param \App\Domain\Project\TemplateValidatorInterface $templateValidator
	 */
	public function __construct(ProjectRepositoryInterface $projectRepository, TemplateValidatorInterface $templateValidator)
	{
		$this->projectRepository = $projectRepository;
		$this->templateValidator = $templateValidator;
	}

	/**
	 * @param \App\Domain\Project\Command\UpdateProjectTemplatesCommand $command
	 *
	 * @return void
	 */
	public function __invoke(UpdateProjectTemplatesCommand $command): void
	{
		$project = $this->projectRepository->get(ProjectId::fromString($command->projectId()));

		foreach ($command->templates() as $locale => $template) {
			$project->changeTemplate(Locale::fromValue($locale), Template::fromValue($template), $this->templateValidator);
		}

		$this->projectRepository->save($project);
	}
}
