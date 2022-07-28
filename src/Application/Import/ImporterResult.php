<?php

declare(strict_types=1);

namespace App\Application\Import;

final class ImporterResult
{
	private bool $ok;

	private string $message;

	private array $warnings = [];

	/**
	 * @param bool   $ok
	 * @param string $message
	 */
	private function __construct(bool $ok, string $message)
	{
		$this->ok = $ok;
		$this->message = $message;
	}

	/**
	 * @param string $message
	 *
	 * @return static
	 */
	public static function success(string $message): self
	{
		return new self(TRUE, $message);
	}

	/**
	 * @param string $message
	 *
	 * @return static
	 */
	public static function error(string $message): self
	{
		return new self(FALSE, $message);
	}

	/**
	 * @param string $warning
	 *
	 * @return $this
	 */
	public function withWarning(string $warning): self
	{
		$result = clone $this;
		$result->warnings[] = $warning;

		return $result;
	}

	/**
	 * @return bool
	 */
	public function ok(): bool
	{
		return $this->ok;
	}

	/**
	 * @return string
	 */
	public function message(): string
	{
		return $this->message;
	}

	/**
	 * @return string[]
	 */
	public function warnings(): array
	{
		return $this->warnings;
	}
}
