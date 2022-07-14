<?php

declare(strict_types=1);

namespace App\Application\Statistics;

use DateTimeImmutable;
use InvalidArgumentException;

final class MultiProjectLastConsentDate
{
	/** @var \DateTimeImmutable[]|NULL[]  */
	private array $lastConsentDates;

	/**
	 * @param \DateTimeImmutable[]|NULL[] $lastConsentDates
	 */
	private function __construct(array $lastConsentDates)
	{
		$this->lastConsentDates = $lastConsentDates;
	}

	/**
	 * @return static
	 */
	public static function create(): self
	{
		return new self([]);
	}

	/**
	 * @param string                  $projectId
	 * @param \DateTimeImmutable|NULL $lastConsentDate
	 *
	 * @return $this
	 */
	public function withDate(string $projectId, ?DateTimeImmutable $lastConsentDate): self
	{
		$lastConsentDates = $this->lastConsentDates;
		$lastConsentDates[$projectId] = $lastConsentDate;

		return new self($lastConsentDates);
	}

	/**
	 * @param string $projectId
	 *
	 * @return \DateTimeImmutable|NULL
	 */
	public function get(string $projectId): ?DateTimeImmutable
	{
		if (!array_key_exists($projectId, $this->lastConsentDates)) {
			throw new InvalidArgumentException(sprintf(
				'Missing last consent date for the project with ID %s.',
				$projectId
			));
		}

		return $this->lastConsentDates[$projectId];
	}

	/**
	 * @param string $projectId
	 *
	 * @return bool
	 */
	public function has(string $projectId): bool
	{
		return isset($this->lastConsentDates[$projectId]);
	}

	/**
	 * @return \DateTimeImmutable[]|NULL[]
	 */
	public function all(): array
	{
		return $this->lastConsentDates;
	}
}
