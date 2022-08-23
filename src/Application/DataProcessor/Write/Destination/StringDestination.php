<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Destination;

final class StringDestination implements DestinationInterface
{
	private string $string;

	private array $options = [];

	private function __construct()
	{
	}

	/**
	 * @param array $options
	 *
	 * @return static
	 */
	public static function create(array $options = []): self
	{
		$destination = new self();
		$destination->string = '';
		$destination->options = $options;

		return $destination;
	}

	/**
	 * @param string $string
	 *
	 * @return $this
	 */
	public function append(string $string): self
	{
		$destination = clone $this;
		$destination->string = $this->string . $string;

		return $destination;
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
