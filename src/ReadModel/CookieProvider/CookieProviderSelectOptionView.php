<?php

declare(strict_types=1);

namespace App\ReadModel\CookieProvider;

use App\Domain\CookieProvider\ValueObject\Name;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class CookieProviderSelectOptionView extends AbstractView
{
	public CookieProviderId $id;

	public Name $name;

	/**
	 * @return array
	 */
	public function toOption(): array
	{
		return [
			$this->id->toString() => $this->name->value(),
		];
	}

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id->toString(),
			'name' => $this->name->value(),
		];
	}
}
