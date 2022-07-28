<?php

declare(strict_types=1);

namespace App\Application\DataReader\Resource;

final class ArrayResource implements ResourceInterface
{
	private array $data;

	private array $options = [];

	private function __construct()
	{
	}

	/**
	 * @param array $data
	 * @param array $options
	 *
	 * @return static
	 */
	public static function create(array $data, array $options = []): self
	{
		$resource = new self();
		$resource->data = $data;
		$resource->options = $options;

		return $resource;
	}

	/**
	 * @return array
	 */
	public function data(): array
	{
		return $this->data;
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
		return 'ARRAY(...)';
	}
}
