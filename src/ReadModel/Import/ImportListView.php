<?php

declare(strict_types=1);

namespace App\ReadModel\Import;

use App\Domain\Import\ValueObject\ImportId;
use App\Domain\Import\ValueObject\Name;
use App\Domain\Import\ValueObject\Status;
use App\Domain\Import\ValueObject\Total;
use DateTimeImmutable;
use DateTimeInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\Name as AuthorName;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;

final class ImportListView extends AbstractView
{
    public ImportId $id;

    public DateTimeImmutable $createdAt;

    public ?DateTimeImmutable $endedAt = null;

    public Name $name;

    public Status $status;

    public Total $imported;

    public Total $failed;

    public Total $warned;

    public ?UserId $authorId = null;

    public ?AuthorName $authorName = null;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
            'endedAt' => $this->endedAt?->format(DateTimeInterface::ATOM),
            'name' => $this->name->value(),
            'status' => $this->status->value(),
            'imported' => $this->imported->value(),
            'failed' => $this->failed->value(),
            'warned' => $this->warned->value(),
            'author_id' => $this->authorId?->toString(),
            'author_name' => $this->authorName?->name(),
        ];
    }
}
