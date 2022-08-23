<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Destination;

class FileDestination implements DestinationInterface
{
	public const OPTION_CHMOD = 'chmod';

	private string $filename;

	private array $options = [
		self::OPTION_CHMOD => 0666,
	];

	private function __construct()
	{
	}

	/**
	 * @param string $filename
	 * @param array  $options
	 *
	 * @return static
	 */
	public static function create(string $filename, array $options = []): self
	{
		$destination = new self();
		$destination->filename = $filename;
		$destination->options = $options;

		return $destination;
	}

	/**
	 * @return string
	 */
	public function filename(): string
	{
		return $this->filename;
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
		return sprintf(
			'FILE(%s)',
			$this->filename()
		);
	}
}
