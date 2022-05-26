<?php

declare(strict_types=1);

namespace App\Domain\Category;

use App\Domain\Category\ValueObject\Code;
use App\Domain\Category\ValueObject\CategoryId;

interface CheckCodeUniquenessInterface
{
	/**
	 * @param \App\Domain\Category\ValueObject\CategoryId $categoryId
	 * @param \App\Domain\Category\ValueObject\Code       $code
	 *
	 * @return void
	 * @throws \App\Domain\Category\Exception\CodeUniquenessException
	 */
	public function __invoke(CategoryId $categoryId, Code $code): void;
}
