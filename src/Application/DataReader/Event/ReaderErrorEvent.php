<?php

declare(strict_types=1);

namespace App\Application\DataReader\Event;

use App\Application\DataReader\Exception\DataReaderExceptionInterface;

final class ReaderErrorEvent
{
	private DataReaderExceptionInterface $error;

	private bool $stopped = FALSE;

	/**
	 * @param \App\Application\DataReader\Exception\DataReaderExceptionInterface $error
	 */
	public function __construct(DataReaderExceptionInterface $error)
	{
		$this->error = $error;
	}

	/**
	 * @return \App\Application\DataReader\Exception\DataReaderExceptionInterface
	 */
	public function error(): DataReaderExceptionInterface
	{
		return $this->error;
	}

	/**
	 * @return bool
	 */
	public function stopped(): bool
	{
		return $this->stopped;
	}

	/**
	 * @return $this
	 */
	public function stop(): self
	{
		$this->stopped = TRUE;

		return $this;
	}
}
