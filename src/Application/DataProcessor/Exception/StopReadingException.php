<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Exception;

use RuntimeException;

final class StopReadingException extends RuntimeException
{
    public static function create(): self
    {
        return new self();
    }
}
