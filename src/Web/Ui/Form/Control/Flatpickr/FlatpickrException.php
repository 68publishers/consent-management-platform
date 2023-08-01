<?php

declare(strict_types=1);

namespace App\Web\Ui\Form\Control\Flatpickr;

use Exception;
use Throwable;

final class FlatpickrException extends Exception
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            empty($message) ? 'Unknown Flatpickr error' : sprintf('Flatpickr Error: %s', $message),
            $code,
            $previous,
        );
    }
}
