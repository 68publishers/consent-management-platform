<?php

declare(strict_types=1);

namespace App\Application\DataReader\Exception;

use RuntimeException;
use App\Application\DataReader\Resource\ResourceInterface;

final class ReaderException extends RuntimeException implements DataReaderExceptionInterface
{
	/**
	 * @param string $message
	 */
	private function __construct(string $message)
	{
		parent::__construct($message);
	}

	/**
	 * @param string $message
	 *
	 * @return static
	 */
	public static function invalidResource(string $message): self
	{
		return new self(sprintf(
			'Invalid resource: %s',
			$message
		));
	}

	/**
	 * @param string                                                 $format
	 * @param \App\Application\DataReader\Resource\ResourceInterface $resource
	 *
	 * @return static
	 */
	public static function unacceptableResource(string $format, ResourceInterface $resource): self
	{
		return new self(sprintf(
			'Unacceptable resource: %s reader doesn\'t accept resource %s',
			$format,
			$resource
		));
	}

	/**
	 * @param string                                                 $format
	 * @param \App\Application\DataReader\Resource\ResourceInterface $resource
	 *
	 * @return static
	 */
	public static function unresolvableResource(string $format, ResourceInterface $resource): self
	{
		return new self(sprintf(
			'Can\'t resolve reader for format %s and resource %s',
			$format,
			$resource
		));
	}
}
