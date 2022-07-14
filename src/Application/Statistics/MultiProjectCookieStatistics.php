<?php

declare(strict_types=1);

namespace App\Application\Statistics;

use InvalidArgumentException;

final class MultiProjectCookieStatistics
{
	/** @var \App\Application\Statistics\CookieStatistics[]  */
	private array $cookiesStatistics;

	/**
	 * @param \App\Application\Statistics\CookieStatistics[] $cookieStatistics
	 */
	private function __construct(array $cookieStatistics)
	{
		$this->cookiesStatistics = $cookieStatistics;
	}

	/**
	 * @return static
	 */
	public static function create(): self
	{
		return new self([]);
	}

	/**
	 * @param string                                       $projectId
	 * @param \App\Application\Statistics\CookieStatistics $cookieStatistics
	 *
	 * @return $this
	 */
	public function withStatistics(string $projectId, CookieStatistics $cookieStatistics): self
	{
		$statistics = $this->cookiesStatistics;
		$statistics[$projectId] = $cookieStatistics;

		return new self($statistics);
	}

	/**
	 * @param string $projectId
	 *
	 * @return \App\Application\Statistics\CookieStatistics
	 * @throws \InvalidArgumentException
	 */
	public function get(string $projectId): CookieStatistics
	{
		if (!isset($this->cookiesStatistics[$projectId])) {
			throw new InvalidArgumentException(sprintf(
				'Missing statistics for the project with ID %s.',
				$projectId
			));
		}

		return $this->cookiesStatistics[$projectId];
	}

	/**
	 * @param string $projectId
	 *
	 * @return bool
	 */
	public function has(string $projectId): bool
	{
		return isset($this->cookiesStatistics[$projectId]);
	}

	/**
	 * @return \App\Application\Statistics\CookieStatistics[]
	 */
	public function all(): array
	{
		return $this->cookiesStatistics;
	}
}
