<?php

declare(strict_types=1);

namespace App\Application\Import;

use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;

final class ImportOptions
{
	private string $describedObjectClassname;

	private string $author;

	private ?LoggerInterface $logger = NULL;

	private bool $async = FALSE;

	private int $batchSize = 10;

	private function __construct()
	{
	}

	/**
	 * @param string $describedObjectClassname
	 *
	 * @return static
	 */
	public static function create(string $describedObjectClassname): self
	{
		$options = new self();
		$options->describedObjectClassname = $describedObjectClassname;
		$options->author = 'system';

		return $options;
	}

	/**
	 * @return string
	 */
	public function describedObjectClassname(): string
	{
		return $this->describedObjectClassname;
	}

	/**
	 * @return string
	 */
	public function author(): string
	{
		return $this->author;
	}

	/**
	 * @return \Psr\Log\LoggerInterface
	 */
	public function logger(): LoggerInterface
	{
		return $this->logger ?? new NullLogger();
	}

	/**
	 * @return bool
	 */
	public function async(): bool
	{
		return $this->async;
	}

	/**
	 * @return int
	 */
	public function batchSize(): int
	{
		return $this->batchSize;
	}

	/**
	 * @param string $author
	 *
	 * @return $this
	 */
	public function withAuthor(string $author): self
	{
		$options = clone $this;
		$options->author = $author;

		return $options;
	}

	/**
	 * @param \Psr\Log\LoggerInterface|NULL $logger
	 *
	 * @return $this
	 */
	public function withLogger(?LoggerInterface $logger): self
	{
		$options = clone $this;
		$options->logger = $logger;

		return $options;
	}

	/**
	 * @param bool $async
	 *
	 * @return $this
	 */
	public function withAsync(bool $async): self
	{
		$options = clone $this;
		$options->async = $async;

		return $options;
	}

	/**
	 * @param int $batchSize
	 *
	 * @return $this
	 */
	public function withBatchSize(int $batchSize): self
	{
		$options = clone $this;
		$options->batchSize = $batchSize;

		return $options;
	}
}
