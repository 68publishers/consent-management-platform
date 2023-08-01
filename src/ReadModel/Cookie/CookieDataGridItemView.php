<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Category\ValueObject\Name as CategoryName;
use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Cookie\ValueObject\Name as CookieName;
use App\Domain\Cookie\ValueObject\ProcessingTime;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\CookieProvider\ValueObject\Name as CookieProviderName;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use DateTimeImmutable;
use DateTimeInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class CookieDataGridItemView extends AbstractView
{
    public CookieId $id;

    public CookieName $cookieName;

    public ProcessingTime $processingTime;

    public bool $active;

    public ?CategoryId $categoryId = null;

    public ?CategoryName $categoryName = null;

    public CookieProviderId $cookieProviderId;

    public CookieProviderName $cookieProviderName;

    public ProviderType $cookieProviderType;

    public bool $cookieProviderPrivate;

    public DateTimeImmutable $createdAt;

    /** @var array<string>|null  */
    public ?array $projects = null;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'cookieName' => $this->cookieName->value(),
            'processingTime' => $this->processingTime->value(),
            'active' => $this->active,
            'categoryId' => $this->categoryId?->toString(),
            'categoryName' => $this->categoryName?->value(),
            'cookieProviderId' => $this->cookieProviderId->toString(),
            'cookieProviderName' => $this->cookieProviderName->value(),
            'cookieProviderType' => $this->cookieProviderType->value(),
            'cookieProviderPrivate' => $this->cookieProviderPrivate,
            'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
            'projects' => $this->projects,
        ];
    }
}
