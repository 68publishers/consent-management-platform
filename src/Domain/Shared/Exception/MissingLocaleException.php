<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Shared\ValueObject\Locales;
use DomainException;

final class MissingLocaleException extends DomainException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function missingLocale(Locales $locales, Locale $missingLocale): self
    {
        return new self(sprintf(
            'Locale %s not found between locales [%s].',
            $missingLocale->value(),
            implode(', ', $locales->toArray()),
        ));
    }
}
