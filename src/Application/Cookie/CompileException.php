<?php

declare(strict_types=1);

namespace App\Application\Cookie;

use RuntimeException;
use Throwable;

final class CompileException extends RuntimeException
{
    /**
     * @return static
     */
    public static function fromPrevious(Throwable $e): self
    {
        return new self($e->getMessage(), $e->getCode(), $e);
    }
}
