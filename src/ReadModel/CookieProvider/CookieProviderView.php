<?php

declare(strict_types=1);

namespace App\ReadModel\CookieProvider;

use DateTimeImmutable;
use DateTimeInterface;
use App\Domain\CookieProvider\ValueObject\Code;
use App\Domain\CookieProvider\ValueObject\Link;
use App\Domain\CookieProvider\ValueObject\Name;
use App\Domain\CookieProvider\ValueObject\Purpose;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class CookieProviderView extends AbstractView
{
	public CookieProviderId $id;

	public DateTimeImmutable $createdAt;

	public ?DateTimeImmutable $deletedAt = NULL;

	public Code $code;

	public ProviderType $type;

	public Name $name;

	public Link $link;

	/** @var \App\Domain\CookieProvider\ValueObject\Purpose[] */
	public array $purposes;

	public bool $private;

	public bool $active;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id->toString(),
			'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM),
			'deletedAt' => NULL !== $this->deletedAt ? $this->deletedAt->format(DateTimeInterface::ATOM) : NULL,
			'code' => $this->code->value(),
			'type' => $this->type->value(),
			'name' => $this->name->value(),
			'link' => $this->link->value(),
			'purposes' => array_map(static fn (Purpose $purpose): string => $purpose->value(), $this->purposes),
			'private' => $this->private,
			'active' => $this->active,
		];
	}
}
