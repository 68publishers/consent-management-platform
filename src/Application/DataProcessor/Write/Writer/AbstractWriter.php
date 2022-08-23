<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Writer;

use Throwable;
use App\Application\DataProcessor\RowInterface;
use App\Application\DataProcessor\Exception\WriterException;
use App\Application\DataProcessor\Write\Resource\ResourceInterface;
use App\Application\DataProcessor\Exception\DataReaderExceptionInterface;
use App\Application\DataProcessor\Write\Destination\DestinationInterface;

abstract class AbstractWriter implements WriterInterface
{
	protected ResourceInterface $resource;

	protected DestinationInterface $destination;

	private bool $locked = FALSE;

	/**
	 * @param \App\Application\DataProcessor\Write\Resource\ResourceInterface       $resource
	 * @param \App\Application\DataProcessor\Write\Destination\DestinationInterface $destination
	 */
	protected function __construct(ResourceInterface $resource, DestinationInterface $destination)
	{
		$this->resource = $resource;
		$this->destination = $destination;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \App\Application\DataProcessor\Exception\DataReaderExceptionInterface
	 */
	public function write(): DestinationInterface
	{
		if ($this->locked) {
			throw WriterException::writerLocked($this->resource, $this->destination);
		}

		$this->locked = TRUE;
		$destination = $this->destination;

		try {
			$this->prepare();

			foreach ($this->resource->rows() as $row) {
				$destination = $this->processRow($row, $destination);
			}

			$destination = $this->finish($destination);
		} catch (DataReaderExceptionInterface $e) {
			throw $e;
		} catch (Throwable $e) {
			throw WriterException::wrap($this->resource, $destination, $e);
		} finally {
			$this->locked = FALSE;
		}

		return $destination;
	}

	/**
	 * @param \App\Application\DataProcessor\RowInterface                           $row
	 * @param \App\Application\DataProcessor\Write\Destination\DestinationInterface $destination
	 *
	 * @return \App\Application\DataProcessor\Write\Destination\DestinationInterface
	 */
	abstract protected function processRow(RowInterface $row, DestinationInterface $destination): DestinationInterface;

	/**
	 * @param \App\Application\DataProcessor\Write\Destination\DestinationInterface $destination
	 *
	 * @return \App\Application\DataProcessor\Write\Destination\DestinationInterface
	 */
	abstract protected function finish(DestinationInterface $destination): DestinationInterface;

	/**
	 * @return void
	 */
	protected function prepare(): void
	{
	}
}
