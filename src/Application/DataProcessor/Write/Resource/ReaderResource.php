<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Resource;

use App\Application\DataProcessor\Read\Reader\ReaderInterface;
use App\Application\DataProcessor\Description\DescriptorInterface;

final class ReaderResource implements ResourceInterface
{
	private ReaderInterface $reader;

	private ?DescriptorInterface $descriptor;

	private $onError;

	/**
	 * @param \App\Application\DataProcessor\Read\Reader\ReaderInterface          $reader
	 * @param \App\Application\DataProcessor\Description\DescriptorInterface|null $descriptor
	 * @param callable|NULL                                                       $onError
	 */
	public function __construct(ReaderInterface $reader, ?DescriptorInterface $descriptor = NULL, ?callable $onError = NULL)
	{
		$this->reader = $reader;
		$this->descriptor = $descriptor;
		$this->onError = $onError;
	}

	/**
	 * {@inheritDoc}
	 */
	public function rows(): iterable
	{
		return $this->reader->read($this->descriptor, $this->onError);
	}

	/**
	 * {@inheritDoc}
	 */
	public function descriptor(): ?DescriptorInterface
	{
		return $this->descriptor;
	}

	/**
	 * {@inheritDoc}
	 */
	public function __toString(): string
	{
		return sprintf(
			'READER(%s%s)',
			get_class($this->reader),
			NULL !== $this->descriptor ? (', ' . get_class($this->descriptor)) : ''
		);
	}
}
