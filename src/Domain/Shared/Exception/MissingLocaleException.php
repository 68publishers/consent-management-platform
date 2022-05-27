<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

use DomainException;
use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Shared\ValueObject\Locales;

final class MissingLocaleException extends DomainException
{
	/**
	 * @param string $message
	 */
	private function __construct(string $message)
	{
		parent::__construct($message);
	}

	/**
	 * @param \App\Domain\Shared\ValueObject\Locales $locales
	 * @param \App\Domain\Shared\ValueObject\Locale  $missingLocale
	 *
	 * @return $this
	 */
	public static function missingLocale(Locales $locales, Locale $missingLocale): self
	{
		return new self(sprintf(
			'Locale %s not found between locales [%s].',
			$missingLocale->value(),
			implode(', ', $locales->toArray())
		));
	}
}
