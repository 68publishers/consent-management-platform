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

	public function store(string $solutionsUniqueId, string $solutionUniqueId, string $solutionType, array $values): void
	{
		$this->sessionSection->set($solutionsUniqueId, [
			'solutionUniqueId' => $solutionUniqueId,
			'solutionType' => $solutionType,
			'values' => $values,
		], $this->expiration);
	}

	public function remove(string $solutionsUniqueId): void
	{
		if ($this->sessionSection->get($solutionsUniqueId)) {
			$this->sessionSection->remove($solutionsUniqueId);
		}
	}

	public function get(string $solutionsUniqueId): ?array
	{
		return $this->sessionSection->get($solutionsUniqueId);
	}
}
