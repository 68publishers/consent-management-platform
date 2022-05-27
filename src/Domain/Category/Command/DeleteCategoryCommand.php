<?php

declare(strict_types=1);

namespace App\Domain\Category\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class DeleteCategoryCommand extends AbstractCommand
{
	/**
	 * @param string $categoryId
	 *
	 * @return static
	 */
	public static function create(string $categoryId): self
	{
		return self::fromParameters([
			'category_id' => $categoryId,
		]);
	}

	/**
	 * @return string
	 */
	public function categoryId(): string
	{
		return $this->getParam('category_id');
	}
}
