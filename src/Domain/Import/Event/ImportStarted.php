<?php

declare(strict_types=1);

namespace App\Domain\Import\Event;

use App\Domain\Import\ValueObject\ImportId;
use App\Domain\Import\ValueObject\Name;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;

final class ImportStarted extends AbstractDomainEvent
{
    private ImportId $id;

    private Name $name;

    private ?UserId $authorId = null;

    /**
     * @return static
     */
    public static function create(ImportId $id, Name $name, ?UserId $authorId): self
    {
        $event = self::occur($id->toString(), [
            'name' => $name->value(),
            'author_id' => $authorId?->toString(),
        ]);

        $event->id = $id;
        $event->name = $name;
        $event->authorId = $authorId;

        return $event;
    }

    public function id(): ImportId
    {
        return $this->id;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function authorId(): ?UserId
    {
        return $this->authorId;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->id = ImportId::fromUuid($this->aggregateId()->id());
        $this->name = Name::fromValue($parameters['name']);
        $this->authorId = null !== $parameters['author_id'] ? UserId::fromString($parameters['author_id']) : null;
    }
}
