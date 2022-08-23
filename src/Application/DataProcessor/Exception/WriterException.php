<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Exception;

use Throwable;
use RuntimeException;
use App\Application\DataProcessor\Write\Resource\ResourceInterface;
use App\Application\DataProcessor\Write\Destination\DestinationInterface;

final class WriterException extends RuntimeException
{
	/**
	 * @param \App\Application\DataProcessor\Write\Resource\ResourceInterface       $resource
	 * @param \App\Application\DataProcessor\Write\Destination\DestinationInterface $destination
	 *
	 * @return static
	 */
	public static function writerLocked(ResourceInterface $resource, DestinationInterface $destination): self
	{
		return new self(sprintf(
			'Can not write the resource %s into the destination %s. Writing process is still in progress.',
			$resource,
			$destination
		));
	}

	/**
	 * @param \App\Application\DataProcessor\Write\Resource\ResourceInterface       $resource
	 * @param \App\Application\DataProcessor\Write\Destination\DestinationInterface $destination
	 * @param \Throwable                                                            $e
	 *
	 * @return static
	 */
	public static function wrap(ResourceInterface $resource, DestinationInterface $destination, Throwable $e): self
	{
		return new self(sprintf(
			'An exception occurred during writing the resource %s into the destination %s: %s',
			$resource,
			$destination,
			$e->getMessage()
		), $e->getCode(), $e);
	}

	/**
	 * @param string                                                                $format
	 * @param \App\Application\DataProcessor\Write\Destination\DestinationInterface $destination
	 *
	 * @return static
	 */
	public static function unacceptableDestination(string $format, DestinationInterface $destination): self
	{
		return new self(sprintf(
			'Unacceptable destination: %s writer doesn\'t accept destination %s',
			$format,
			$destination
		));
	}

	/**
	 * @param string                                                                $format
	 * @param \App\Application\DataProcessor\Write\Destination\DestinationInterface $destination
	 *
	 * @return static
	 */
	public static function unresolvableDestination(string $format, DestinationInterface $destination): self
	{
		return new self(sprintf(
			'Can\'t resolve writer for format %s and destination %s',
			$format,
			$destination
		));
	}
}
