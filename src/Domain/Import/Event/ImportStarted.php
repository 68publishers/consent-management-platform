<?php

declare(strict_types=1);

namespace App\Domain\Import\Event;

use App\Domain\Import\ValueObject\Name;
use App\Domain\Import\ValueObject\ImportId;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ImportStarted extends AbstractDomainEvent
{
	private ImportId $id;

	private Name $name;

	private ?UserId $authorId = NULL;

	/**
	 * @param \App\Domain\Import\ValueObject\ImportId                         $id
	 * @param \App\Domain\Import\ValueObject\Name                             $name
	 * @param \SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId|NULL $authorId
	 *
	 * @return static
	 */
	public static function create(ImportId $id, Name $name, ?UserId $authorId): self
	{
		$event = self::occur($id->toString(), [
			'name' => $name->value(),
			'author_id' => NULL !== $authorId ? $authorId->toString() : NULL,
		]);

		$event->id = $id;
		$event->name = $name;
		$event->authorId = $authorId;

		return $event;
	}

	/**
	 * @return \App\Domain\Import\ValueObject\ImportId
	 */
	public function id(): ImportId
	{
		return $this->id;
	}

	/**
	 * @return \App\Domain\Import\ValueObject\Name
	 */
	public function name(): Name
	{
		return $this->name;
	}

	/**
	 * @return \SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId|NULL
	 */
	public function authorId(): ?UserId
	{
		return $this->authorId;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->id = ImportId::fromUuid($this->aggregateId()->id());
		$this->name = Name::fromValue($parameters['name']);
		$this->authorId = NULL !== $parameters['author_id'] ? UserId::fromString($parameters['author_id']) : NULL;
	}
}
