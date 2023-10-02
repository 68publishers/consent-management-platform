<?php

declare(strict_types=1);

namespace App\ReadModel\Cookie;

use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Cookie\ValueObject\Domain;
use App\Domain\Cookie\ValueObject\Environments;
use App\Domain\Cookie\ValueObject\Name;
use App\Domain\Cookie\ValueObject\ProcessingTime;
use App\Domain\Cookie\ValueObject\Purpose;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use DateTimeImmutable;
use DateTimeInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class CookieView extends AbstractView
{
    public CookieId $id;

    public CategoryId $categoryId;

    public CookieProviderId $cookieProviderId;

    public DateTimeImmutable $createdAt;

    public ?DateTimeImmutable $deletedAt = null;

    public Name $name;

    public Domain $domain;

    public ProcessingTime $processingTime;

    public bool $active;

    /** @var array<Purpose> */
    public array $purposes;

    public bool $allEnvironments;

    public Environments $environments;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'categoryId' => $this->categoryId->toString(),
            'cookieProviderId' => $this->cookieProviderId->toString(),
            'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
            'deletedAt' => $this->deletedAt?->format(DateTimeInterface::ATOM),
            'name' => $this->name->value(),
            'domain' => $this->domain->value(),
            'processingTime' => $this->processingTime->value(),
            'active' => $this->active,
            'purposes' => array_map(static fn (Purpose $purpose): string => $purpose->value(), $this->purposes),
            'allEnvironments' => $this->allEnvironments,
            'environments' => $this->environments->toArray(),
        ];
    }
}
