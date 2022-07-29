<?php

declare(strict_types=1);

namespace App\Domain\Import\Event;

use App\Domain\Import\ValueObject\Total;
use App\Domain\Import\ValueObject\Output;
use App\Domain\Import\ValueObject\ImportId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ImportCompleted extends AbstractDomainEvent
{
	private ImportId $id;

	private Total $imported;

	private Total $failed;

	private Total $warned;

	private Output $output;

	/**
	 * @param \App\Domain\Import\ValueObject\ImportId $id
	 * @param \App\Domain\Import\ValueObject\Total    $imported
	 * @param \App\Domain\Import\ValueObject\Total    $failed
	 * @param \App\Domain\Import\ValueObject\Total    $warned
	 * @param \App\Domain\Import\ValueObject\Output   $output
	 *
	 * @return static
	 */
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

	/**
	 * @return \App\Domain\Import\ValueObject\ImportId
	 */
	public function id(): ImportId
	{
		return $this->id;
	}

	/**
	 * @return \App\Domain\Import\ValueObject\Total
	 */
	public function imported(): Total
	{
		return $this->imported;
	}

	/**
	 * @return \App\Domain\Import\ValueObject\Total
	 */
	public function failed(): Total
	{
		return $this->failed;
	}

	/**
	 * @return \App\Domain\Import\ValueObject\Total
	 */
	public function warned(): Total
	{
		return $this->warned;
	}

	/**
	 * @return \App\Domain\Import\ValueObject\Output
	 */
	public function output(): Output
	{
		return $this->output;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->id = ImportId::fromUuid($this->aggregateId()->id());
		$this->imported = Total::fromValue($parameters['imported']);
		$this->failed = Total::fromValue($parameters['failed']);
		$this->warned = Total::fromValue($parameters['warned']);
		$this->output = Output::fromValue($parameters['output']);
	}
}
