<?php

declare(strict_types=1);

namespace App\Domain\Project\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class UpdateProjectTemplatesCommand extends AbstractCommand
{
	/**
	 * @param string $projectId
	 *
	 * @return static
	 */
	public static function create(string $projectId): self
	{
		return self::fromParameters([
			'project_id' => $projectId,
			'templates' => [],
		]);
	}

	/**
	 * @param string $locale
	 * @param string $template
	 *
	 * @return $this
	 */
	public function withTemplate(string $locale, string $template): self
	{
		$templates = $this->templates();
		$templates[$locale] = $template;

		return $this->withParam('templates', $templates);
	}

	/**
	 * @return string
	 */
	public function projectId(): string
	{
		return $this->getParam('project_id');
	}

	/**
	 * @return array
	 */
	public function templates(): array
	{
		return $this->getParam('templates');
	}
}
