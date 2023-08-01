<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Reader;

use App\Application\DataProcessor\Exception\DataReaderExceptionInterface;
use App\Application\DataProcessor\Exception\StopReadingException;
use App\Application\DataProcessor\Read\Event\ReaderErrorEvent;

final class ErrorCallback
{
    /** @var callable|NULL */
    private $callback;

    private function __construct() {}

    public static function wrap(?callable $callback = null): self
    {
        $errorCallback = new self();
        $errorCallback->callback = $callback;

        return $errorCallback;
    }

    /**
     * @throws DataReaderExceptionInterface
     */
    public function __invoke(DataReaderExceptionInterface $exception): void
    {
        if (null === $this->callback) {
            throw $exception;
        }

        $event = new ReaderErrorEvent($exception);

        ($this->callback)($event);

        if ($event->stopped()) {
            throw StopReadingException::create();
        }
    }
}
