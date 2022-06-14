<?php

declare(strict_types=1);

namespace App\Domain\Cookie;

use App\Domain\Category\ValueObject\CategoryId;

interface CheckCategoryExistsInterface
{
	/**
	 * @param \App\Domain\Category\ValueObject\CategoryId $categoryId
	 *
	 * @return void
	 * @throws \App\Domain\Category\Exception\CategoryNotFoundException
	 */
	public function __invoke(CategoryId $categoryId): void;
}
