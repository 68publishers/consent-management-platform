<?php

declare(strict_types=1);

namespace App\Domain\Consent\Event;

use App\Domain\Consent\ValueObject\ConsentId;
use App\Domain\Consent\ValueObject\Attributes;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class AttributesChanged extends AbstractDomainEvent
{
	private ConsentId $consentId;

	private Attributes $attributes;

	/**
	 * @param \App\Domain\Consent\ValueObject\ConsentId  $consentId
	 * @param \App\Domain\Consent\ValueObject\Attributes $attributes
	 *
	 * @return static
	 */
	public static function create(ConsentId $consentId, Attributes $attributes): self
	{
		$event = self::occur($consentId->toString(), [
			'attributes' => $attributes->values(),
		]);

		$event->consentId = $consentId;
		$event->attributes = $attributes;

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
	 * @return \App\Domain\Consent\ValueObject\Attributes
	 */
	public function attributes(): Attributes
	{
		return $this->attributes;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->consentId = ConsentId::fromUuid($this->aggregateId()->id());
		$this->attributes = Attributes::fromArray($parameters['attributes']);
	}
}
