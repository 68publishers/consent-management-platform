<?php

declare(strict_types=1);

namespace App\Domain\Import\Exception;

use DomainException;
use App\Domain\Import\ValueObject\ImportId;

final class InvalidStatusChangeException extends DomainException
{
	/**
	 * @param \App\Domain\Import\ValueObject\ImportId $importId
	 *
	 * @return static
	 */
	public static function unableToFail(ImportId $importId): self
	{
		return new self(sprintf(
			'Unable to fail the import with ID %s',
			$importId->toString()
		));
	}

	/**
	 * @param \App\Domain\Import\ValueObject\ImportId $importId
	 *
	 * @return static
	 */
	public static function unableToComplete(ImportId $importId): self
	{
		return new self(sprintf(
			'Unable to complete the import with ID %s',
			$importId->toString()
		));
	}
}
