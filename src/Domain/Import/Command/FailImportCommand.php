<?php

declare(strict_types=1);

namespace App\Domain\Import\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class FailImportCommand extends AbstractCommand
{
	/**
	 * @param string $id
	 * @param int    $imported
	 * @param int    $failed
	 * @param int    $warned
	 * @param string $output
	 *
	 * @return static
	 */
	public static function create(string $id, int $imported, int $failed, int $warned, string $output): self
	{
		return self::fromParameters([
			'id' => $id,
			'imported' => $imported,
			'failed' => $failed,
			'warned' => $warned,
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
	 * @return int
	 */
	public function warned(): int
	{
		return $this->getParam('warned');
	}

	/**
	 * @return string
	 */
	public function output(): string
	{
		return $this->getParam('output');
	}
}