<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Read\Reader;

use App\Application\DataProcessor\Exception\ReaderException;
use App\Application\DataProcessor\Read\Resource\QueryResource;
use App\Application\DataProcessor\Read\Resource\ResourceInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;

final class QueryReaderFactory implements ReaderFactoryInterface
{
	private QueryBusInterface $queryBus;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface $queryBus
	 */
	public function __construct(QueryBusInterface $queryBus)
	{
		$this->queryBus = $queryBus;
	}

	/**
	 * {@inheritDoc}
	 */
	public function accepts(string $format, ResourceInterface $resource): bool
	{
		return $resource instanceof QueryResource; // ignore a format
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(ResourceInterface $resource): ReaderInterface
	{
		if ($resource instanceof QueryResource) {
			return QueryReader::create($this->queryBus, $resource);
		}

		throw ReaderException::unacceptableResource('query', $resource);
	}
}
