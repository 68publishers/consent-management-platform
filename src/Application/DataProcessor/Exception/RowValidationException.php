<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Exception;

use RuntimeException;

final class RowValidationException extends RuntimeException implements DataReaderExceptionInterface
{
    private string $rowIndex;

    private function __construct(string $rowIndex, string $message)
    {
        $this->rowIndex = $rowIndex;

        parent::__construct($message);
    }

    public static function error(string $rowIndex, string $error): self
    {
        return new self($rowIndex, sprintf(
            '[:%s] %s',
            $rowIndex,
            $error,
        ));
    }

    public function rowIndex(): string
    {
        return $this->rowIndex;
    }
}
