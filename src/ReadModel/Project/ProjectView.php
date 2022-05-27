<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use App\Domain\Project\ValueObject\Code;
use App\Domain\Project\ValueObject\Name;
use App\Domain\Project\ValueObject\Color;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Project\ValueObject\Description;
use App\Domain\Shared\ValueObject\LocalesConfig;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class ProjectView extends AbstractView
{
	public ProjectId $id;

	public Name $name;

	public Code $code;

	public Color $color;

	public Description $description;

	public bool $active;

	public LocalesConfig $locales;

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id->toString(),
			'name' => $this->name->value(),
			'code' => $this->code->value(),
			'color' => $this->color->value(),
			'description' => $this->description->value(),
			'active' => $this->active,
			'locales' => $this->locales->locales()->toArray(),
			'defaultLocale' => $this->locales->defaultLocale()->value(),
		];
	}
}
