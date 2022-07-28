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
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\CookieProvider\ValueObject\Name as CookieProviderName;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class CookieDataGridItemView extends AbstractView
{
	public CookieId $id;

	public CookieName $cookieName;

	public ProcessingTime $processingTime;

	public bool $active;

	public ?CategoryId $categoryId = NULL;

	public ?CategoryName $categoryName = NULL;

	public ?CookieProviderId $cookieProviderId = NULL;

	public ?CookieProviderName $cookieProviderName = NULL;

	public DateTimeImmutable $createdAt;

	/** @var string[]|NULL  */
	public ?array $projects = NULL;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id->toString(),
			'cookieName' => $this->cookieName->value(),
			'processingTime' => $this->processingTime->value(),
			'active' => $this->active,
			'categoryId' => NULL !== $this->categoryId ? $this->categoryId->toString() : NULL,
			'categoryName' => NULL !== $this->categoryName ? $this->categoryName->value() : NULL,
			'cookieProviderId' => NULL !== $this->cookieProviderId ? $this->cookieProviderId->toString() : NULL,
			'cookieProviderName' => NULL !== $this->cookieProviderName ? $this->cookieProviderName->value() : NULL,
			'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
			'projects' => $this->projects,
		];
	}
}