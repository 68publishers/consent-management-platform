<?php

declare(strict_types=1);

namespace App\ReadModel\CookieProvider;

use App\Domain\CookieProvider\ValueObject\Code;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\CookieProvider\ValueObject\Link;
use App\Domain\CookieProvider\ValueObject\Name;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use DateTimeImmutable;
use DateTimeInterface;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class CookieProviderDaraGridItemView extends AbstractView
{
    public CookieProviderId $id;

    public DateTimeImmutable $createdAt;

    public Code $code;

    public ProviderType $type;

    public Name $name;

    public Link $link;

    public bool $private;

    public bool $active;

    public int $numberOfCookies;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
            'code' => $this->code->value(),
            'type' => $this->type->value(),
            'name' => $this->name->value(),
            'link' => $this->link->value(),
            'private' => $this->private,
            'active' => $this->active,
            'numberOfCookie' => $this->numberOfCookies,
        ];
    }
}
