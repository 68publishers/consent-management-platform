<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

use DateTimeImmutable;
use DateTimeInterface;
use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Cookie\ValueObject\ProcessingTime;
use App\Domain\Cookie\ValueObject\Name as CookieName;
use App\Domain\Category\ValueObject\Name as CategoryName;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class CookieItemView extends AbstractView
{
	public CookieId $id;

	public CookieName $cookieName;

	public ProcessingTime $processingTime;

	public ?CategoryId $categoryId = NULL;

	public ?CategoryName $categoryName = NULL;

	public DateTimeImmutable $createdAt;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id->toString(),
			'cookie_name' => $this->cookieName->value(),
			'processing_time' => $this->processingTime->value(),
			'category_id' => NULL !== $this->categoryId ? $this->categoryId->toString() : NULL,
			'category_name' => NULL !== $this->categoryName ? $this->categoryName->value() : NULL,
			'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
		];
	}
}
