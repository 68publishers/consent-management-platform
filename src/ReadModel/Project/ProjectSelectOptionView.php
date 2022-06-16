<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use App\Domain\Project\ValueObject\Name;
use App\Domain\Project\ValueObject\ProjectId;
use SixtyEightPublishers\ArchitectureBundle\ReadModel\View\AbstractView;

final class ProjectSelectOptionView extends AbstractView
{
	public ProjectId $id;

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
