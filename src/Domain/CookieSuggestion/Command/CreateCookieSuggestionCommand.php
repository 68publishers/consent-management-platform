<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class CreateCookieSuggestionCommand extends AbstractCommand
{
	/**
	 * @param array<int, CookieOccurrence> $occurrences
	 */
	public static function create(
		string $projectId,
		string $name,
		string $domain,
		array $occurrences,
		?string $cookieSuggestionId = NULL
	): self {
		return self::fromParameters([
			'project_id' => $projectId,
			'name' => $name,
			'domain' => $domain,
			'occurrences' => $occurrences,
			'cookie_suggestion_id' => $cookieSuggestionId,
		]);
	}

	public function projectId(): string
	{
		return $this->getParam('project_id');
	}

	public function name(): string
	{
		return $this->getParam('name');
	}

	public function domain(): string
	{
		return $this->getParam('domain');
	}

	/**
	 * @return array<int, CookieOccurrence>
	 */
	public function occurrences(): array
	{
		return $this->getParam('occurrences');
	}

	public function cookieSuggestionId(): ?string
	{
		return $this->getParam('cookie_suggestion_id');
	}
}
