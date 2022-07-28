<?php

declare(strict_types=1);

namespace App\Application\DataReader\Reader;

use Nette\Schema\Schema;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use App\Application\DataReader\ArrayRowData;
use App\Application\DataReader\RowInterface;
use App\Application\DataReader\Context\Context;
use App\Application\DataReader\RowDataInterface;
use App\Application\DataReader\Context\ContextInterface;
use App\Application\DataReader\Resource\ResourceInterface;
use App\Application\DataReader\Exception\StopReadingException;
use App\Application\DataReader\Description\DescriptorInterface;
use App\Application\DataReader\Exception\RowValidationException;

abstract class AbstractReader implements ReaderInterface
{
	protected ResourceInterface $resource;

	/**
	 * @param \App\Application\DataReader\Resource\ResourceInterface $resource
	 */
	protected function __construct(ResourceInterface $resource)
	{
		$this->resource = $resource;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \App\Application\DataReader\Exception\DataReaderExceptionInterface
	 */
	public function read(?DescriptorInterface $descriptor = NULL, ?callable $onError = NULL): iterable
	{
		$processor = new Processor();
		$schema = NULL !== $descriptor ? $descriptor->getSchema($this->createContext()) : NULL;
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
	 * @param \App\Application\DataReader\Reader\ErrorCallback $errorCallback
	 *
	 * @return iterable|\App\Application\DataReader\RowInterface[]
	 * @throws \App\Application\DataReader\Exception\DataReaderExceptionInterface
	 */
	abstract protected function doRead(ErrorCallback $errorCallback): iterable;

	/**
	 * @return \App\Application\DataReader\Context\ContextInterface
	 */
	protected function createContext(): ContextInterface
	{
		return Context::default();
	}

	/**
	 * @param \App\Application\DataReader\RowInterface         $row
	 * @param \Nette\Schema\Processor                          $processor
	 * @param \Nette\Schema\Schema                             $schema
	 * @param \App\Application\DataReader\Reader\ErrorCallback $errorCallback
	 *
	 * @return \App\Application\DataReader\RowInterface|NULL
	 * @throws \App\Application\DataReader\Exception\DataReaderExceptionInterface
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
