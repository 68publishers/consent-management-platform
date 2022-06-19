<?php

declare(strict_types=1);

namespace App\ReadModel\CookieProvider;

use App\Domain\CookieProvider\ValueObject\Link;
use App\Domain\CookieProvider\ValueObject\Name;
use App\Domain\CookieProvider\ValueObject\Purpose;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class CookieProviderApiView extends AbstractView
{
	public Name $name;

	public ProviderType $type;

	public Link $link;

	public ?Purpose $purpose = NULL;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'name' => $this->name->value(),
			'type' => $this->type->value(),
			'link' => $this->link->value(),
			'purpose' => NULL !== $this->purpose ? $this->purpose->value() : '',
		];
	}
}
