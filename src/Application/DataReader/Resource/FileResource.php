<?php

declare(strict_types=1);

namespace App\Application\DataReader\Resource;

use App\Application\DataReader\Exception\IoException;

final class FileResource implements ResourceInterface
{
	private string $filename;

	private array $options = [];

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
		$resource = new self();
		$resource->filename = $filename;
		$resource->options = $options;

		return $resource;
	}

	/**
	 * @return string
	 */
	public function filename(): string
	{
		return $this->filename;
	}

	/**
	 * @return string|NULL
	 */
	public function extension(): ?string
	{
		$pathInfo = pathinfo($this->filename());

		return $pathInfo['extension'] ?? NULL;
	}

	/**
	 * @return bool
	 */
	public function exists(): bool
	{
		return file_exists($this->filename());
	}

	/**
	 * @return string
	 */
	public function content(): string
	{
		if (!$this->exists()) {
			throw IoException::fileNotFound($this->filename());
		}

		$content = @file_get_contents($this->filename());

		if (FALSE === $content) {
			throw IoException::fileNotReadable($this->filename());
		}

		return $content;
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
