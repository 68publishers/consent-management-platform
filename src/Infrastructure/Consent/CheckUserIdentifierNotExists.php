<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent;

use App\ReadModel\Consent\ConsentView;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Consent\ValueObject\UserIdentifier;
use App\Domain\Consent\CheckUserIdentifierNotExistsInterface;
use App\Domain\Consent\Exception\UserIdentifierExistsException;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\ReadModel\Consent\GetConsentByProjectIdAndUserIdentifierQuery;

final class CheckUserIdentifierNotExists implements CheckUserIdentifierNotExistsInterface
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
	public function __invoke(UserIdentifier $userIdentifier, ProjectId $projectId): void
	{
		$consentView = $this->queryBus->dispatch(GetConsentByProjectIdAndUserIdentifierQuery::create($projectId->toString(), $userIdentifier->value()));

		if ($consentView instanceof ConsentView) {
			throw UserIdentifierExistsException::create($consentView->id, $userIdentifier);
		}
	}
}
