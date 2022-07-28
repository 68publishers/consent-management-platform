<?php

declare(strict_types=1);

namespace App\Domain\Import\Event;

use App\Domain\Import\ValueObject\Name;
use App\Domain\Import\ValueObject\Author;
use App\Domain\Import\ValueObject\ImportId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ImportStarted extends AbstractDomainEvent
{
	private ImportId $id;

	private Name $name;

	private Author $author;

	/**
	 * @param \App\Domain\Import\ValueObject\ImportId $id
	 * @param \App\Domain\Import\ValueObject\Name     $name
	 * @param \App\Domain\Import\ValueObject\Author   $author
	 *
	 * @return static
	 */
	public static function create(ImportId $id, Name $name, Author $author): self
	{
		$event = self::occur($id->toString(), [
			'name' => $name->value(),
			'author' => $author->value(),
		]);

		$event->id = $id;
		$event->name = $name;
		$event->author = $author;

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
	 * @return \App\Domain\Import\ValueObject\Author
	 */
	public function author(): Author
	{
		return $this->author;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->id = ImportId::fromUuid($this->aggregateId()->id());
		$this->name = Name::fromValue($parameters['name']);
		$this->author = Author::fromValue($parameters['author']);
	}
}
