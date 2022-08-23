<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write;

use App\Application\DataProcessor\Write\Writer\WriterInterface;
use App\Application\DataProcessor\Write\Resource\ResourceInterface;
use App\Application\DataProcessor\Write\Destination\DestinationInterface;

interface DataWriterFactoryInterface
{
	/**
	 * @param string                                                                $format
	 * @param \App\Application\DataProcessor\Write\Resource\ResourceInterface       $resource
	 * @param \App\Application\DataProcessor\Write\Destination\DestinationInterface $destination
	 *
	 * @return \App\Application\DataProcessor\Write\Writer\WriterInterface
	 */
	public function toDestination(string $format, ResourceInterface $resource, DestinationInterface $destination): WriterInterface;

	/**
	 * @param string                                                          $format
	 * @param \App\Application\DataProcessor\Write\Resource\ResourceInterface $resource
	 * @param string                                                          $filename
	 * @param array                                                           $options
	 *
	 * @return \App\Application\DataProcessor\Write\Writer\WriterInterface
	 */
	public function toFile(string $format, ResourceInterface $resource, string $filename, array $options = []): WriterInterface;

	/**
	 * @param string                                                          $format
	 * @param \App\Application\DataProcessor\Write\Resource\ResourceInterface $resource
	 * @param array                                                           $options
	 *
	 * @return \App\Application\DataProcessor\Write\Writer\WriterInterface
	 */
	public function toString(string $format, ResourceInterface $resource, array $options = []): WriterInterface;
}
