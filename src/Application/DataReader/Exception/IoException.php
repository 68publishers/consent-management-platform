<?php

declare(strict_types=1);

namespace App\Application\DataReader\Exception;

use RuntimeException;

final class IoException extends RuntimeException
{
	/**
	 * @param string $message
	 */
	private function __construct(string $message)
	{
		parent::__construct($message);
	}

	/**
	 * @param string $filename
	 *
	 * @return static
	 */
	public static function fileNotFound(string $filename): self
	{
		return new self(sprintf(
			'File %s not found',
			$filename
		));
	}

	/**
	 * @param string $filename
	 *
	 * @return static
	 */
	public static function fileNotReadable(string $filename): self
	{
		return new self(sprintf(
			'Unable to read file %s',
			$filename
		));
	}
}
