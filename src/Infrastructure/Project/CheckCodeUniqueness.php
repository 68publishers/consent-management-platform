<?php

declare(strict_types=1);

namespace App\Infrastructure\Project;

use App\ReadModel\Project\ProjectView;
use App\Domain\Project\ValueObject\Code;
use App\Domain\Project\ValueObject\ProjectId;
use App\ReadModel\Project\GetProjectByCodeQuery;
use App\Domain\Project\CheckCodeUniquenessInterface;
use App\Domain\Project\Exception\CodeUniquenessException;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;

final class CheckCodeUniqueness implements CheckCodeUniquenessInterface
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
	public function __invoke(ProjectId $projectId, Code $code): void
	{
		$projectView = $this->queryBus->dispatch(GetProjectByCodeQuery::create($code->value()));

		if (!$projectView instanceof ProjectView) {
			return;
		}

		if (!$projectView->id->equals($projectId)) {
			throw CodeUniquenessException::create($code->value());
		}
	}
}
