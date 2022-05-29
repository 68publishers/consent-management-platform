<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CategoryForm\Event;

use Symfony\Contracts\EventDispatcher\Event;
use App\Domain\Category\ValueObject\CategoryId;

final class CategoryUpdatedEvent extends Event
{
	private CategoryId $categoryId;

	private string $oldCode;

	private string $newCode;

	/**
	 * @param \App\Domain\Category\ValueObject\CategoryId $categoryId
	 * @param string                                      $oldCode
	 * @param string                                      $newCode
	 */
	public function __construct(CategoryId $categoryId, string $oldCode, string $newCode)
	{
		$this->categoryId = $categoryId;
		$this->oldCode = $oldCode;
		$this->newCode = $newCode;
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
	public function oldCode(): string
	{
		return $this->oldCode;
	}

	/**
	 * @return string
	 */
	public function newCode(): string
	{
		return $this->newCode;
	}
}
