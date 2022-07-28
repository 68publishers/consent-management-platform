<?php

declare(strict_types=1);

namespace App\Application\DataReader;

use App\Application\DataReader\Reader\ReaderInterface;
use App\Application\DataReader\Resource\ResourceInterface;

interface DataReaderFactoryInterface
{
	/**
	 * @param string                                                 $format
	 * @param \App\Application\DataReader\Resource\ResourceInterface $resource
	 *
	 * @return \App\Application\DataReader\Reader\ReaderInterface
	 */
	public function fromResource(string $format, ResourceInterface $resource): ReaderInterface;

	/**
	 * @param string $format
	 * @param string $filename
	 * @param array  $options
	 *
	 * @return \App\Application\DataReader\Reader\ReaderInterface
	 */
	public function fromFile(string $format, string $filename, array $options = []): ReaderInterface;

	/**
	 * @param string $format
	 * @param string $string
	 * @param array  $options
	 *
	 * @return \App\Application\DataReader\Reader\ReaderInterface
	 */
	public function fromString(string $format, string $string, array $options = []): ReaderInterface;

	/**
	 * @param array $data
	 * @param array $options
	 *
	 * @return \App\Application\DataReader\Reader\ReaderInterface
	 */
	public function fromArray(array $data, array $options = []): ReaderInterface;
}
