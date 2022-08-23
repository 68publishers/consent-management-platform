<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Write\Writer;

use App\Application\DataProcessor\Write\Destination\DestinationInterface;

interface WriterInterface
{
	/**
	 * @return \App\Application\DataProcessor\Write\Destination\DestinationInterface
	 */
	public function write(): DestinationInterface;
}
