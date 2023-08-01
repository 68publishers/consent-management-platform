<?php

declare(strict_types=1);

namespace App\Domain\Import\Exception;

use App\Domain\Import\ValueObject\ImportId;
use DomainException;

final class InvalidStatusChangeException extends DomainException
{
    /**
     * @return static
     */
    public static function unableToFail(ImportId $importId): self
    {
        return new self(sprintf(
            'Unable to fail the import with ID %s',
            $importId->toString(),
        ));
    }

    /**
     * @return static
     */
    public static function unableToComplete(ImportId $importId): self
    {
        return new self(sprintf(
            'Unable to complete the import with ID %s',
            $importId->toString(),
        ));
    }
}
