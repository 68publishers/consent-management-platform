<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Writer;

use App\Application\DataProcessor\Write\Resource\ResourceInterface;
use App\Application\DataProcessor\Write\Destination\DestinationInterface;

interface WriterFactoryInterface
{
	/**
	 * @param string                                                                $format
	 * @param \App\Application\DataProcessor\Write\Destination\DestinationInterface $destination
	 *
	 * @return bool
	 */
	public function accepts(string $format, DestinationInterface $destination): bool;

	/**
	 * @param \App\Application\DataProcessor\Write\Resource\ResourceInterface       $resource
	 * @param \App\Application\DataProcessor\Write\Destination\DestinationInterface $destination
	 *
	 * @return \App\Application\DataProcessor\Write\Writer\WriterInterface
	 * @throws \App\Application\DataProcessor\Exception\WriterException
	 */
	public function create(ResourceInterface $resource, DestinationInterface $destination): WriterInterface;
}
