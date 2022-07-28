<?php

declare(strict_types=1);

namespace App\Application\DataReader\Exception;

use RuntimeException;

final class RowValidationException extends RuntimeException implements DataReaderExceptionInterface
{
	private string $rowIndex;

	/**
	 * @param string $rowIndex
	 * @param string $message
	 */
	private function __construct(string $rowIndex, string $message)
	{
		$this->rowIndex = $rowIndex;

		parent::__construct($message);
	}

	/**
	 * @param string $rowIndex
	 * @param string $error
	 *
	 * @return static
	 */
	public static function error(string $rowIndex, string $error): self
	{
		return new self($rowIndex, sprintf(
			'[:%s] %s',
			$rowIndex,
			$error
		));
	}

	/**
	 * @return string
	 */
	public function rowIndex(): string
	{
		return $this->rowIndex;
	}
}
