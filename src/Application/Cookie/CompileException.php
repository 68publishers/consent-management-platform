<?php

declare(strict_types=1);

namespace App\Application\Cookie;

use Throwable;
use RuntimeException;

final class CompileException extends RuntimeException
{
	/**
	 * @param \Throwable $e
	 *
	 * @return static
	 */
	public static function fromPrevious(Throwable $e): self
	{
		return new self($e->getMessage(), $e->getCode(), $e);
	}
}
