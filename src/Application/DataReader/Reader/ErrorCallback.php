<?php

declare(strict_types=1);

namespace App\Application\DataReader\Reader;

use App\Application\DataReader\Event\ReaderErrorEvent;
use App\Application\DataReader\Exception\StopReadingException;
use App\Application\DataReader\Exception\DataReaderExceptionInterface;

final class ErrorCallback
{
	/** @var callable|NULL */
	private $callback;

	private function __construct()
	{
	}

	/**
	 * @param callable|NULL $callback
	 *
	 * @return static
	 */
	public static function wrap(?callable $callback = NULL): self
	{
		$errorCallback = new self();
		$errorCallback->callback = $callback;

		return $errorCallback;
	}

	/**
	 * @param \App\Application\DataReader\Exception\DataReaderExceptionInterface $exception
	 *
	 * @return void
	 * @throws \App\Application\DataReader\Exception\DataReaderExceptionInterface
	 */
	public function __invoke(DataReaderExceptionInterface $exception): void
	{
		if (NULL === $this->callback) {
			throw $exception;
		}

		$event = new ReaderErrorEvent($exception);

		($this->callback)($event);

		if ($event->stopped()) {
			throw StopReadingException::create();
		}
	}
}
