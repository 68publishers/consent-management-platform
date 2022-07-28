<?php

declare(strict_types=1);

namespace App\Domain\Import\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class StartImportCommand extends AbstractCommand
{
	/**
	 * @param string $id
	 * @param string $name
	 * @param string $author
	 *
	 * @return static
	 */
	public static function create(string $id, string $name, string $author): self
	{
		return self::fromParameters([
			'id' => $id,
			'name' => $name,
			'author' => $author,
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
	public function author(): string
	{
		return $this->getParam('author');
	}
}
