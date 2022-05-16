<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentHistory;

use App\Web\Ui\Control;
use App\Domain\Consent\Consent;
use App\Domain\Consent\ValueObject\ConsentId;
use SixtyEightPublishers\ArchitectureBundle\EventStore\EventCriteria;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;
use SixtyEightPublishers\ArchitectureBundle\EventStore\EventStoreInterface;

final class ConsentHistoryControl extends Control
{
	private ConsentId $consentId;

	private EventStoreInterface $eventStore;

	/**
	 * @param \App\Domain\Consent\ValueObject\ConsentId                               $consentId
	 * @param \SixtyEightPublishers\ArchitectureBundle\EventStore\EventStoreInterface $eventStore
	 */
	public function __construct(ConsentId $consentId, EventStoreInterface $eventStore)
	{
		$this->consentId = $consentId;
		$this->eventStore = $eventStore;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$criteria = EventCriteria::create(Consent::class)
			->withAggregateId(AggregateId::fromUuid($this->consentId->id()))
			->withNewestSorting();

		$this->template->events = $this->eventStore->find($criteria);
	}
}
