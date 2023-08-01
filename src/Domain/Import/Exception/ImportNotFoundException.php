<?php

declare(strict_types=1);

namespace App\Domain\Import\Exception;

use App\Domain\Import\ValueObject\ImportId;
use DomainException;

final class ImportNotFoundException extends DomainException
{
    public static function withId(ImportId $id): self
    {
        return new self(sprintf(
            'Import with ID %s not found.',
            $id,
        ));
    }
}
