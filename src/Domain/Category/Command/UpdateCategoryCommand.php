<?php

declare(strict_types=1);

namespace App\Domain\Category\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class UpdateCategoryCommand extends AbstractCommand
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
	 * @return string|NULL
	 */
	public function categoryId(): string
	{
		return $this->getParam('category_id');
	}

	/**
	 * @return string|NULL
	 */
	public function code(): ?string
	{
		return $this->getParam('code');
	}

	/**
	 * @return array|NULL
	 */
	public function names(): ?array
	{
		return $this->getParam('names');
	}

	/**
	 * @return bool|NULL
	 */
	public function active(): ?bool
	{
		return $this->getParam('active');
	}

	/**
	 * @return bool|NULL
	 */
	public function necessary(): ?bool
	{
		return $this->getParam('necessary');
	}

	/**
	 * @param string $code
	 *
	 * @return $this
	 */
	public function withCode(string $code): self
	{
		return $this->withParam('code', $code);
	}

	/**
	 * @param array $names
	 *
	 * @return $this
	 */
	public function withNames(array $names): self
	{
		return $this->withParam('names', $names);
	}

	/**
	 * @param bool $active
	 *
	 * @return $this
	 */
	public function withActive(bool $active): self
	{
		return $this->withParam('active', $active);
	}

	/**
	 * @param bool $necessary
	 *
	 * @return $this
	 */
	public function withNecessary(bool $necessary): self
	{
		return $this->withParam('necessary', $necessary);
	}
}
