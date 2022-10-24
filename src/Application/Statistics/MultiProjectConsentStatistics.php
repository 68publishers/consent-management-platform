<?php

declare(strict_types=1);

namespace App\Application\Statistics;

use InvalidArgumentException;

final class MultiProjectConsentStatistics
{
	/** @var \App\Application\Statistics\ConsentStatistics[]  */
	private array $consentStatistics;

	/**
	 * @param \App\Application\Statistics\ConsentStatistics[] $consentStatistics
	 */
	private function __construct(array $consentStatistics)
	{
		$this->consentStatistics = $consentStatistics;
	}

	/**
	 * @return static
	 */
	public static function create(): self
	{
		return new self([]);
	}

	/**
	 * @param string                                        $projectId
	 * @param \App\Application\Statistics\ConsentStatistics $consentStatistics
	 *
	 * @return $this
	 */
	public function withStatistics(string $projectId, ConsentStatistics $consentStatistics): self
	{
		$statistics = $this->consentStatistics;
		$statistics[$projectId] = $consentStatistics;

		return new self($statistics);
	}

	/**
	 * @param string $projectId
	 *
	 * @return \App\Application\Statistics\ConsentStatistics
	 * @throws \InvalidArgumentException
	 */
	public function get(string $projectId): ConsentStatistics
	{
		if (!isset($this->consentStatistics[$projectId])) {
			throw new InvalidArgumentException(sprintf(
				'Missing statistics for the project with ID %s.',
				$projectId
			));
		}

		return $this->consentStatistics[$projectId];
	}

	/**
	 * @return \App\Application\Statistics\ConsentStatistics[]
	 */
	public function all(): array
	{
		return $this->consentStatistics;
	}
}
