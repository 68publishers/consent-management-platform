<?php

declare(strict_types=1);

namespace App\Application\CookieSuggestion\DataStore;

use Nette\Http\Session;
use Nette\Http\SessionSection;

final class SessionDataStore implements DataStoreInterface
{
	private SessionSection $sessionSection;

	private string $expiration;

	public function __construct(Session $session, string $expiration = '+1 hour')
	{
		$this->sessionSection = $session->getSection(self::class);
		$this->expiration = $expiration;
	}

	public function store(string $projectId, string $solutionsUniqueId, string $solutionUniqueId, string $solutionType, string $cookieSuggestionId, array $values): void
	{
		$section = $this->getAll($projectId);
		$section[$solutionsUniqueId] = [
			'solutionUniqueId' => $solutionUniqueId,
			'solutionType' => $solutionType,
			'cookieSuggestionId' => $cookieSuggestionId,
			'values' => $values,
		];

		$this->sessionSection->set($projectId, $section);
	}

	public function remove(string $projectId, string $solutionsUniqueId): void
	{
		$section = $this->getAll($projectId);

		if (isset($section[$solutionsUniqueId])) {
			unset($section[$solutionsUniqueId]);

			$this->sessionSection->set($projectId, $section, $this->expiration);
		}
	}

	public function removeAll(string $projectId): void
	{
		if ($this->sessionSection->get($projectId)) {
			$this->sessionSection->remove($projectId);
		}
	}

	public function get(string $projectId, string $solutionsUniqueId): ?array
	{
		$section = $this->getAll($projectId);

		return $section[$solutionsUniqueId] ?? NULL;
	}

	public function getAll(string $projectId): array
	{
		return $this->sessionSection->get($projectId) ?? [];
	}
}
