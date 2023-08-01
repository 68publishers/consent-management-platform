<?php

declare(strict_types=1);

namespace App\Domain\Import;

use App\Domain\Import\Command\CompleteImportCommand;
use App\Domain\Import\Command\FailImportCommand;
use App\Domain\Import\Command\StartImportCommand;
use App\Domain\Import\Event\ImportCompleted;
use App\Domain\Import\Event\ImportFailed;
use App\Domain\Import\Event\ImportStarted;
use App\Domain\Import\Exception\InvalidStatusChangeException;
use App\Domain\Import\ValueObject\ImportId;
use App\Domain\Import\ValueObject\Name;
use App\Domain\Import\ValueObject\Output;
use App\Domain\Import\ValueObject\Status;
use App\Domain\Import\ValueObject\Total;
use DateTimeImmutable;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootInterface;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootTrait;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;

final class Import implements AggregateRootInterface
{
    use AggregateRootTrait;

    private ImportId $id;

    private UserId $authorId;

    private DateTimeImmutable $createdAt;

    private ?DateTimeImmutable $endedAt = null;

    private Name $name;

    private Status $status;

    private Total $imported;

    private Total $failed;

    private Total $warned;

    private Output $output;

    public static function create(StartImportCommand $command): self
    {
        $import = new self();

        $import->recordThat(ImportStarted::create(
            ImportId::fromString($command->id()),
            Name::fromValue($command->name()),
            null !== $command->authorId() ? UserId::fromString($command->authorId()) : null,
        ));

        return $import;
    }

    public function aggregateId(): AggregateId
    {
        return AggregateId::fromUuid($this->id->id());
    }

    public function fail(FailImportCommand $command): void
    {
        $id = ImportId::fromString($command->id());

        if (!$this->id->equals($id) || !$this->status->is(Status::RUNNING)) {
            throw InvalidStatusChangeException::unableToFail($this->id);
        }

        $this->recordThat(ImportFailed::create(
            $id,
            Total::fromValue($command->imported()),
            Total::fromValue($command->failed()),
            Total::fromValue($command->warned()),
            Output::fromValue($command->output()),
        ));
    }

    public function complete(CompleteImportCommand $command): void
    {
        $id = ImportId::fromString($command->id());

        if (!$this->id->equals($id) || !$this->status->is(Status::RUNNING)) {
            throw InvalidStatusChangeException::unableToComplete($this->id);
        }

        $this->recordThat(ImportCompleted::create(
            $id,
            Total::fromValue($command->imported()),
            Total::fromValue($command->failed()),
            Total::fromValue($command->warned()),
            Output::fromValue($command->output()),
        ));
    }

    protected function whenImportStarted(ImportStarted $event): void
    {
        $this->id = $event->id();
        $this->createdAt = $event->createdAt();
        $this->name = $event->name();
        $this->status = Status::running();
        $this->authorId = $event->authorId();
        $this->imported = Total::fromValue(0);
        $this->failed = Total::fromValue(0);
        $this->warned = Total::fromValue(0);
        $this->output = Output::fromValue('');
    }

    protected function whenImportFailed(ImportFailed $event): void
    {
        $this->endedAt = $event->createdAt();
        $this->status = Status::failed();
        $this->imported = $event->imported();
        $this->failed = $event->failed();
        $this->warned = $event->warned();
        $this->output = $event->output();
    }

    protected function whenImportCompleted(ImportCompleted $event): void
    {
        $this->endedAt = $event->createdAt();
        $this->status = Status::completed();
        $this->imported = $event->imported();
        $this->failed = $event->failed();
        $this->warned = $event->warned();
        $this->output = $event->output();
    }
}
