<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings\Exception;

use DomainException;
use Throwable;

final class ShortIdentifierGeneratorException extends DomainException
{
    public static function from(Throwable $e): self
    {
        return new self(sprintf(
            'Can\'t generate short identifier: %s',
            $e->getMessage(),
        ), $e->getCode(), $e);
    }
}
