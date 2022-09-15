<?php

declare(strict_types=1);

namespace App\ReadModel\Import;

use DateTimeImmutable;
use DateTimeInterface;
use App\Domain\Import\ValueObject\Name;
use App\Domain\Import\ValueObject\Total;
use App\Domain\Import\ValueObject\Status;
use App\Domain\Import\ValueObject\ImportId;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\Name as AuthorName;

final class ImportListView extends AbstractView
{
	public ImportId $id;

	public DateTimeImmutable $createdAt;

	public ?DateTimeImmutable $endedAt = NULL;

	public Name $name;

	public Status $status;

	public Total $imported;

	public Total $failed;

	public Total $warned;

	public ?UserId $authorId = NULL;

	public ?AuthorName $authorName = NULL;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id->toString(),
			'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
			'endedAt' => NULL !== $this->endedAt ? $this->endedAt->format(DateTimeInterface::ATOM) : NULL,
			'name' => $this->name->value(),
			'status' => $this->status->value(),
			'imported' => $this->imported->value(),
			'failed' => $this->failed->value(),
			'warned' => $this->warned->value(),
			'author_id' => NULL !== $this->authorId ? $this->authorId->toString() : NULL,
			'author_name' => NULL !== $this->authorName ? $this->authorName->name() : NULL,
		];
	}
}
