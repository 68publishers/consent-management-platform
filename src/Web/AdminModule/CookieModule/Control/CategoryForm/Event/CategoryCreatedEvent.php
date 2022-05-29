<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CategoryForm\Event;

use Symfony\Contracts\EventDispatcher\Event;
use App\Domain\Category\ValueObject\CategoryId;

final class CategoryCreatedEvent extends Event
{
	private CategoryId $categoryId;

	private string $code;

	/**
	 * @param \App\Domain\Category\ValueObject\CategoryId $categoryId
	 * @param string                                      $code
	 */
	public function __construct(CategoryId $categoryId, string $code)
	{
		$this->categoryId = $categoryId;
		$this->code = $code;
	}

	/**
	 * @return \App\Domain\Category\ValueObject\CategoryId
	 */
	public function categoryId(): CategoryId
	{
		return $this->categoryId;
	}

	/**
	 * @return string
	 */
	public function code(): string
	{
		return $this->code;
	}
}
