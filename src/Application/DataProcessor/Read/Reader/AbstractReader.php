<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Reader;

use Nette\Schema\Schema;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use App\Application\DataProcessor\ArrayRowData;
use App\Application\DataProcessor\RowInterface;
use App\Application\DataProcessor\Context\Context;
use App\Application\DataProcessor\RowDataInterface;
use App\Application\DataProcessor\Context\ContextInterface;
use App\Application\DataProcessor\Write\Resource\ReaderResource;
use App\Application\DataProcessor\Exception\StopReadingException;
use App\Application\DataProcessor\Description\DescriptorInterface;
use App\Application\DataProcessor\Read\Resource\ResourceInterface;
use App\Application\DataProcessor\Exception\RowValidationException;
use App\Application\DataProcessor\Write\Resource\ResourceInterface as WritableResource;

abstract class AbstractReader implements ReaderInterface
{
	protected ResourceInterface $resource;

	/**
	 * @param \App\Application\DataProcessor\Read\Resource\ResourceInterface $resource
	 */
	protected function __construct(ResourceInterface $resource)
	{
		$this->resource = $resource;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \App\Application\DataProcessor\Exception\DataReaderExceptionInterface
	 */
	public function read(?DescriptorInterface $descriptor = NULL, ?callable $onError = NULL): iterable
	{
		$processor = new Processor();
		$schema = NULL !== $descriptor ? $descriptor->schema($this->createContext()) : NULL;
		$errorCallback = ErrorCallback::wrap($onError);

		try {
			foreach ($this->doRead($errorCallback) as $row) {
				if (NULL !== $schema) {
					$row = $this->normalizeRow($row, $processor, $schema, $errorCallback);
				}

				if (NULL !== $row) {
					yield $row;
				}
			}
		} catch (StopReadingException $e) {
			return;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function toWritableResource(?DescriptorInterface $descriptor = NULL, ?callable $onError = NULL): WritableResource
	{
		return new ReaderResource($this, $descriptor, $onError);
	}

	/**
	 * @param \App\Application\DataProcessor\Read\Reader\ErrorCallback $errorCallback
	 *
	 * @return iterable|\App\Application\DataProcessor\RowInterface[]
	 * @throws \App\Application\DataProcessor\Exception\DataReaderExceptionInterface
	 */
	abstract protected function doRead(ErrorCallback $errorCallback): iterable;

	/**
	 * @return \App\Application\DataProcessor\Context\ContextInterface
	 */
	protected function createContext(): ContextInterface
	{
		return Context::default();
	}

	/**
	 * @param \App\Application\DataProcessor\RowInterface              $row
	 * @param \Nette\Schema\Processor                                  $processor
	 * @param \Nette\Schema\Schema                                     $schema
	 * @param \App\Application\DataProcessor\Read\Reader\ErrorCallback $errorCallback
	 *
	 * @return \App\Application\DataProcessor\RowInterface|NULL
	 * @throws \App\Application\DataProcessor\Exception\DataReaderExceptionInterface
	 */
	protected function normalizeRow(RowInterface $row, Processor $processor, Schema $schema, ErrorCallback $errorCallback): ?RowInterface
	{
		try {
			$normalized = $processor->process($schema, $row->data()->toArray());

			if (!$normalized instanceof RowDataInterface) {
				$normalized = ArrayRowData::create((array) $normalized);
			}

			return $row->withData($normalized);
		} catch (ValidationException $e) {
			foreach ($e->getMessages() as $message) {
				$errorCallback(RowValidationException::error($row->index(), $message));
			}
		}

		return NULL;
	}
}
