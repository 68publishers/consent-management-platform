<?php

declare(strict_types=1);

namespace App\ReadModel\Import;

use DateTimeImmutable;
use DateTimeInterface;
use App\Domain\Import\ValueObject\Name;
use App\Domain\Import\ValueObject\Total;
use App\Domain\Import\ValueObject\Author;
use App\Domain\Import\ValueObject\Output;
use App\Domain\Import\ValueObject\Status;
use App\Domain\Import\ValueObject\ImportId;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class ImportView extends AbstractView
{
	public ImportId $id;

	public DateTimeImmutable $createdAt;

	public ?DateTimeImmutable $endedAt = NULL;

	public Name $name;

	public Status $status;

	public Author $author;

	public Total $imported;

	public Total $failed;

	public Output $output;

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
			'author' => $this->author->value(),
			'imported' => $this->imported->value(),
			'failed' => $this->failed->value(),
			'output' => $this->output->value(),
		];
	}
}
