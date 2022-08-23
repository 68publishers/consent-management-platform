<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description\Path;

use Countable;

final class Path implements Countable
{
	private array $parts;

	private function __construct()
	{
	}

	/**
	 * @param array $parts
	 *
	 * @return static
	 */
	public static function fromParts(array $parts): self
	{
		$path = new self();
		$path->parts = $parts;

		return $path;
	}

	/**
	 * @param string $pathString
	 *
	 * @return static
	 */
	public static function fromString(string $pathString): self
	{
		$path = new self();
		$path->parts = !empty($pathString) ? explode('.', $pathString) : [];

		return $path;
	}

	/**
	 * @return string|NULL
	 */
	public function shift(): ?string
	{
		return array_shift($this->parts);
	}

	/**
	 * @return array
	 */
	public function parts(): array
	{
		return $this->parts;
	}

	/**
	 * @return int
	 */
	public function count(): int
	{
		return count($this->parts);
	}
}
