<?php

declare(strict_types=1);

namespace App\Domain\Import\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class StartImportCommand extends AbstractCommand
{
	/**
	 * @param string      $id
	 * @param string      $name
	 * @param string|NULL $authorId
	 *
	 * @return static
	 */
	public static function create(string $id, string $name, ?string $authorId = NULL): self
	{
		return self::fromParameters([
			'id' => $id,
			'name' => $name,
			'author_id' => $authorId,
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
	 * @return string
	 */
	public function name(): string
	{
		return $this->getParam('name');
	}

	/**
	 * @return string
	 */
	public function authorId(): ?string
	{
		return $this->getParam('author_id');
	}
}
