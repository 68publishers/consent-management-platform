<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\Exception;

use App\Domain\GlobalSettings\ValueObject\Environments;
use DomainException;

final class UnableToCreateEnvironmentsException extends DomainException
{
    public static function nativeMustBeArray(): self
    {
        return new self(sprintf(
            'Unable to create %s from a native value. The native value must be an array.',
            Environments::class,
        ));
    }

    public function cannotIncludeDefaultEnvironment(): self
    {
        return new self(sprintf(
            'Object %s can not include an environment with the code "default".',
            Environments::class,
        ));
    }
}
