<?php

declare(strict_types=1);

namespace App\Domain\Import\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class CompleteImportCommand extends AbstractCommand
{
	/**
	 * @param string $id
	 * @param int    $imported
	 * @param int    $failed
	 * @param string $output
	 *
	 * @return static
	 */
	public static function create(string $id, int $imported, int $failed, string $output): self
	{
		return self::fromParameters([
			'id' => $id,
			'imported' => $imported,
			'failed' => $failed,
			'output' => $output,
		]);
	}

	/**
	 * @return string
	 */
	public function id(): string
	{
		return $this->getParam('id');
	}

	/**
	 * @return int
	 */
	public function imported(): int
	{
		return $this->getParam('imported');
	}

	/**
	 * @return int
	 */
	public function failed(): int
	{
		return $this->getParam('failed');
	}

	/**
	 * @return string
	 */
	public function output(): string
	{
		return $this->getParam('output');
	}
}
