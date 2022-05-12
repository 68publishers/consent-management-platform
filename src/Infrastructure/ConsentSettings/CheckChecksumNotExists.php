<?php

declare(strict_types=1);

namespace App\Infrastructure\ConsentSettings;

use App\Domain\Shared\ValueObject\Checksum;
use App\Domain\Project\ValueObject\ProjectId;
use App\ReadModel\ConsentSettings\ConsentSettingsView;
use App\Domain\ConsentSettings\CheckChecksumNotExistsInterface;
use App\Domain\ConsentSettings\Exception\ChecksumExistsException;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use App\ReadModel\ConsentSettings\GetConsentSettingsByProjectIdAndChecksumQuery;

final class CheckChecksumNotExists implements CheckChecksumNotExistsInterface
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
	public function __invoke(ProjectId $projectId, Checksum $checksum): void
	{
		$consentSettingsView = $this->queryBus->dispatch(GetConsentSettingsByProjectIdAndChecksumQuery::create($projectId->toString(), $checksum->value()));

		if ($consentSettingsView instanceof ConsentSettingsView) {
			throw ChecksumExistsException::create($consentSettingsView->id, $consentSettingsView->checksum);
		}
	}
}
