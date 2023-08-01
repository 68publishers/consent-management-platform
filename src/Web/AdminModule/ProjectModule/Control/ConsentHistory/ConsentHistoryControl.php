<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentHistory;

use App\Domain\Consent\Consent;
use App\Domain\Consent\Event\ConsentCreated;
use App\Domain\Consent\Event\ConsentUpdated;
use App\Domain\Consent\ValueObject\ConsentId;
use App\Domain\Project\ValueObject\ProjectId;
use App\ReadModel\ConsentSettings\ConsentSettingsView;
use App\ReadModel\ConsentSettings\GetConsentSettingsByProjectIdAndChecksumQuery;
use App\Web\Ui\Control;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;
use SixtyEightPublishers\ArchitectureBundle\EventStore\EventCriteria;
use SixtyEightPublishers\ArchitectureBundle\EventStore\EventStoreException;
use SixtyEightPublishers\ArchitectureBundle\EventStore\EventStoreInterface;

final class ConsentHistoryControl extends Control
{
    public function __construct(
        private readonly ConsentId $consentId,
        private readonly ProjectId $projectId,
        private readonly EventStoreInterface $eventStore,
        private readonly QueryBusInterface $queryBus,
    ) {}

    /**
     * @throws EventStoreException
     */
    protected function beforeRender(): void
    {
        parent::beforeRender();

        $consentSettingsShortIdentifiers = [];

        $criteria = EventCriteria::create(Consent::class)
            ->withAggregateId(AggregateId::fromUuid($this->consentId->id()))
            ->withNewestSorting();

        $events = $this->eventStore->find($criteria);

        foreach ($events as $event) {
            if (!$event instanceof ConsentCreated && !$event instanceof ConsentUpdated) {
                continue;
            }

            $checksum = $event->settingsChecksum();

            if (null !== $checksum && !array_key_exists($checksum->value(), $consentSettingsShortIdentifiers)) {
                $consentSettingsView = $this->queryBus->dispatch(GetConsentSettingsByProjectIdAndChecksumQuery::create($this->projectId->toString(), $checksum->value()));
                $consentSettingsShortIdentifiers[$checksum->value()] = $consentSettingsView instanceof ConsentSettingsView ? $consentSettingsView->shortIdentifier->value() : null;
            }
        }

        $template = $this->getTemplate();
        assert($template instanceof ConsentHistoryTemplate);

        $template->events = $events;
        $template->consentSettingsShortIdentifiers = $consentSettingsShortIdentifiers;
    }
}
