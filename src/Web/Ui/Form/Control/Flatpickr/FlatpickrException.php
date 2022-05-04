<?php

declare(strict_types=1);

namespace App\Web\Ui\Form\Control\Flatpickr;

use Exception;
use Throwable;

final class FlatpickrException extends Exception
{
	/**
	 * @param string          $message
	 * @param int             $code
	 * @param \Throwable|NULL $previous
	 */
	public function __construct(string $message = '', int $code = 0, Throwable $previous = NULL)
	{
		parent::__construct(
			empty($message) ? 'Unknown Flatpickr error' : sprintf('Flatpickr Error: %s', $message),
			$code,
			$previous
		);
	}
}
