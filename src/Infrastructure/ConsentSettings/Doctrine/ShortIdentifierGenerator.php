<?php

declare(strict_types=1);

namespace App\Infrastructure\ConsentSettings\Doctrine;

use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\ConsentSettings\ValueObject\ShortIdentifier;
use App\ReadModel\ConsentSettings\GetLatestShortIdentifierQuery;
use App\Domain\ConsentSettings\ShortIdentifierGeneratorInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;

final class ShortIdentifierGenerator implements ShortIdentifierGeneratorInterface
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
	public function generate(ProjectId $projectId): ShortIdentifier
	{
		return ShortIdentifier::fromValue(
			$this->queryBus->dispatch(GetLatestShortIdentifierQuery::create($projectId->toString())) + 1
		);
	}
}
