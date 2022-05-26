<?php

declare(strict_types=1);

namespace App\Domain\Category;

use App\Domain\Category\ValueObject\CategoryId;

interface CategoryRepositoryInterface
{
	/**
	 * @param \App\Domain\Category\Category $category
	 *
	 * @return void
	 */
	public function save(Category $category): void;

	/**
	 * @param \App\Domain\Category\ValueObject\CategoryId $id
	 *
	 * @return \App\Domain\Category\Category
	 * @throws \App\Domain\Category\Exception\CategoryNotFoundException
	 */
	public function get(CategoryId $id): Category;
}
