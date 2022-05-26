<?php

declare(strict_types=1);

namespace App\Domain\Category\Exception;

use DomainException;
use App\Domain\Category\ValueObject\CategoryId;

final class CategoryNotFoundException extends DomainException
{
	/**
	 * @param \App\Domain\Category\ValueObject\CategoryId $id
	 *
	 * @return static
	 */
	public static function withId(CategoryId $id): self
	{
		return new self(sprintf(
			'Category with ID %s not found.',
			$id
		));
	}
}
