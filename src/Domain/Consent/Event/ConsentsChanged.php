<?php

declare(strict_types=1);

namespace App\Domain\Consent\Event;

use App\Domain\Consent\ValueObject\Consents;
use App\Domain\Consent\ValueObject\ConsentId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ConsentsChanged extends AbstractDomainEvent
{
	private ConsentId $consentId;

	private Consents $consents;

	/**
	 * @param \App\Domain\Consent\ValueObject\ConsentId $consentId
	 * @param \App\Domain\Consent\ValueObject\Consents  $consents
	 *
	 * @return static
	 */
	public static function create(ConsentId $consentId, Consents $consents): self
	{
		$event = self::occur($consentId->toString(), [
			'consents' => $consents->values(),
		]);

		$event->consentId = $consentId;
		$event->consents = $consents;

		return $event;
	}

	/**
	 * @return \App\Domain\Consent\ValueObject\ConsentId
	 */
	public function consentId(): ConsentId
	{
		return $this->consentId;
	}

	/**
	 * @return \App\Domain\Consent\ValueObject\Consents
	 */
	public function consents(): Consents
	{
		return $this->consents;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->consentId = ConsentId::fromUuid($this->aggregateId()->id());
		$this->consents = Consents::fromArray($parameters['consents']);
	}
}
