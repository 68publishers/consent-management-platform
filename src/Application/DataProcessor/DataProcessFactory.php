<?php

declare(strict_types=1);

namespace App\Application\DataProcessor;

use App\Application\DataProcessor\Read\DataReaderFactoryInterface;
use App\Application\DataProcessor\Read\Resource\ResourceInterface;
use App\Application\DataProcessor\Write\DataWriterFactoryInterface;

final class DataProcessFactory
{
	private DataReaderFactoryInterface $dataReaderFactory;

	private DataWriterFactoryInterface $dataWriterFactory;

	/**
	 * @param \App\Application\DataProcessor\Read\DataReaderFactoryInterface  $dataReaderFactory
	 * @param \App\Application\DataProcessor\Write\DataWriterFactoryInterface $dataWriterFactory
	 */
	public function __construct(DataReaderFactoryInterface $dataReaderFactory, DataWriterFactoryInterface $dataWriterFactory)
	{
		$this->dataReaderFactory = $dataReaderFactory;
		$this->dataWriterFactory = $dataWriterFactory;
	}

	/**
	 * @param string                                                         $format
	 * @param \App\Application\DataProcessor\Read\Resource\ResourceInterface $resource
	 *
	 * @return \App\Application\DataProcessor\WriteProcess
	 */
	public function fromResource(string $format, ResourceInterface $resource): WriteProcess
	{
		return new WriteProcess($this->dataWriterFactory, $this->dataReaderFactory->fromResource($format, $resource));
	}

	/**
	 * @param string $format
	 * @param string $filename
	 * @param array  $options
	 *
	 * @return \App\Application\DataProcessor\WriteProcess
	 */
	public function fromFile(string $format, string $filename, array $options = []): WriteProcess
	{
		return new WriteProcess($this->dataWriterFactory, $this->dataReaderFactory->fromFile($format, $filename, $options));
	}

	/**
	 * @param string $format
	 * @param string $string
	 * @param array  $options
	 *
	 * @return \App\Application\DataProcessor\WriteProcess
	 */
	public function fromString(string $format, string $string, array $options = []): WriteProcess
	{
		return new WriteProcess($this->dataWriterFactory, $this->dataReaderFactory->fromString($format, $string, $options));
	}

	/**
	 * @param array $data
	 * @param array $options
	 *
	 * @return \App\Application\DataProcessor\WriteProcess
	 */
	public function fromArray(array $data, array $options = []): WriteProcess
	{
		return new WriteProcess($this->dataWriterFactory, $this->dataReaderFactory->fromArray($data, $options));
	}
}
