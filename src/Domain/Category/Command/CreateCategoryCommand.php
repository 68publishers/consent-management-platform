<?php

declare(strict_types=1);

namespace App\Domain\Category\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class CreateCategoryCommand extends AbstractCommand
{
	/**
	 * @param string      $code
	 * @param array       $names
	 * @param bool        $active
	 * @param bool        $necessary
	 * @param string|NULL $categoryId
	 *
	 * @return static
	 */
	public static function create(string $code, array $names, bool $active, bool $necessary, ?string $categoryId = NULL): self
	{
		return self::fromParameters([
			'code' => $code,
			'names' => $names,
			'active' => $active,
			'necessary' => $necessary,
			'category_id' => $categoryId,
		]);
	}

	/**
	 * @return string
	 */
	public function code(): string
	{
		return $this->getParam('code');
	}

	/**
	 * @return array
	 */
	public function names(): array
	{
		return $this->getParam('names');
	}

	/**
	 * @return bool
	 */
	public function active(): bool
	{
		return $this->getParam('active');
	}

	/**
	 * @return bool
	 */
	public function necessary(): bool
	{
		return $this->getParam('necessary');
	}

	/**
	 * @return string|NULL
	 */
	public function categoryId(): ?string
	{
		return $this->getParam('category_id');
	}
}
