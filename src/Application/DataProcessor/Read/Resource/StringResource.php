<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Resource;

final class StringResource implements ResourceInterface
{
	private string $string;

	private array $options = [];

	private function __construct()
	{
	}

	/**
	 * @param string $string
	 * @param array  $options
	 *
	 * @return static
	 */
	public static function create(string $string, array $options = []): self
	{
		$resource = new self();
		$resource->string = $string;
		$resource->options = $options;

		return $resource;
	}

	/**
	 * @return string
	 */
	public function string(): string
	{
		return $this->string;
	}

	/**
	 * {@inheritDoc}
	 */
	public function options(): array
	{
		return $this->options;
	}

	/**
	 * {@inheritDoc}
	 */
	public function __toString(): string
	{
		return 'STRING(...)';
	}
}
