<?php

declare(strict_types=1);

namespace App\Application\Statistics;

use InvalidArgumentException;

final class MultiProjectConsentPeriodStatistics
{
	/** @var \App\Application\Statistics\ConsentPeriodStatistics[]  */
	private array $consentPeriodStatistics;

	/**
	 * @param \App\Application\Statistics\ConsentPeriodStatistics[] $consentPeriodStatistics
	 */
	private function __construct(array $consentPeriodStatistics)
	{
		$this->consentPeriodStatistics = $consentPeriodStatistics;
	}

	/**
	 * @return static
	 */
	public static function create(): self
	{
		return new self([]);
	}

	/**
	 * @param string                                              $projectId
	 * @param \App\Application\Statistics\ConsentPeriodStatistics $consentPeriodStatistics
	 *
	 * @return $this
	 */
	public function withStatistics(string $projectId, ConsentPeriodStatistics $consentPeriodStatistics): self
	{
		$statistics = $this->consentPeriodStatistics;
		$statistics[$projectId] = $consentPeriodStatistics;

		return new self($statistics);
	}

	/**
	 * @param string $projectId
	 *
	 * @return \App\Application\Statistics\ConsentPeriodStatistics
	 * @throws \InvalidArgumentException
	 */
	public function get(string $projectId): ConsentPeriodStatistics
	{
		if (!isset($this->consentPeriodStatistics[$projectId])) {
			throw new InvalidArgumentException(sprintf(
				'Missing statistics for the project with ID %s.',
				$projectId
			));
		}

		return $this->consentPeriodStatistics[$projectId];
	}

	/**
	 * @return \App\Application\Statistics\ConsentPeriodStatistics[]
	 */
	public function all(): array
	{
		return $this->consentPeriodStatistics;
	}
}
