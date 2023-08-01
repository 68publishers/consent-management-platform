<?php

declare(strict_types=1);

namespace App\Domain\Import\Event;

use App\Domain\Import\ValueObject\ImportId;
use App\Domain\Import\ValueObject\Output;
use App\Domain\Import\ValueObject\Total;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ImportCompleted extends AbstractDomainEvent
{
    private ImportId $id;

    private Total $imported;

    private Total $failed;

    private Total $warned;

    private Output $output;

    public static function create(ImportId $id, Total $imported, Total $failed, Total $warned, Output $output): self
    {
        $event = self::occur($id->toString(), [
            'imported' => $imported->value(),
            'failed' => $failed->value(),
            'warned' => $warned->value(),
            'output' => $output->value(),
        ]);

        $event->id = $id;
        $event->imported = $imported;
        $event->failed = $failed;
        $event->warned = $warned;
        $event->output = $output;

        return $event;
    }

    public function id(): ImportId
    {
        return $this->id;
    }

    public function imported(): Total
    {
        return $this->imported;
    }

    public function failed(): Total
    {
        return $this->failed;
    }

    public function warned(): Total
    {
        return $this->warned;
    }

    public function output(): Output
    {
        return $this->output;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->id = ImportId::fromUuid($this->aggregateId()->id());
        $this->imported = Total::fromValue($parameters['imported']);
        $this->failed = Total::fromValue($parameters['failed']);
        $this->warned = Total::fromValue($parameters['warned']);
        $this->output = Output::fromValue($parameters['output']);
    }
}
