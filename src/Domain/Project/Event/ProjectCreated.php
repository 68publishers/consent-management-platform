<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use App\Domain\Project\ValueObject\Code;
use App\Domain\Project\ValueObject\Name;
use App\Domain\Project\ValueObject\Color;
use App\Domain\Shared\ValueObject\Locales;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Project\ValueObject\Description;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectCreated extends AbstractDomainEvent
{
	private ProjectId $projectId;

	private Name $name;

	private Code $code;

	private Description $description;

	private Color $color;

	private bool $active;

	private Locales $locales;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId   $projectId
	 * @param \App\Domain\Project\ValueObject\Name        $name
	 * @param \App\Domain\Project\ValueObject\Code        $code
	 * @param \App\Domain\Project\ValueObject\Description $description
	 * @param \App\Domain\Project\ValueObject\Color       $color
	 * @param bool                                        $active
	 * @param \App\Domain\Shared\ValueObject\Locales      $locales
	 *
	 * @return static
	 */
	public static function create(ProjectId $projectId, Name $name, Code $code, Description $description, Color $color, bool $active, Locales $locales): self
	{
		$event = self::occur($projectId->toString(), [
			'name' => $name->value(),
			'code' => $code->value(),
			'description' => $description->value(),
			'color' => $color->value(),
			'active' => $active,
			'locales' => $locales->toArray(),
		]);

		$event->projectId = $projectId;
		$event->name = $name;
		$event->code = $code;
		$event->description = $description;
		$event->color = $color;
		$event->active = $active;
		$event->locales = $locales;

		return $event;
	}

	/**
	 * @return \App\Domain\Project\ValueObject\ProjectId
	 */
	public function projectId(): ProjectId
	{
		return $this->projectId;
	}

	/**
	 * @return \App\Domain\Project\ValueObject\Name
	 */
	public function name(): Name
	{
		return $this->name;
	}

	/**
	 * @return \App\Domain\Project\ValueObject\Code
	 */
	public function code(): Code
	{
		return $this->code;
	}

	/**
	 * @return \App\Domain\Project\ValueObject\Description
	 */
	public function description(): Description
	{
		return $this->description;
	}

	/**
	 * @return \App\Domain\Project\ValueObject\Color
	 */
	public function color(): Color
	{
		return $this->color;
	}

	/**
	 * @return bool
	 */
	public function active(): bool
	{
		return $this->active;
	}

	/**
	 * @return \App\Domain\Shared\ValueObject\Locales
	 */
	public function locales(): Locales
	{
		return $this->locales;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
		$this->name = Name::fromValue($parameters['name']);
		$this->code = Code::fromValue($parameters['code']);
		$this->description = Description::fromValue($parameters['description']);
		$this->color = Color::fromValue($parameters['color']);
		$this->active = (bool) $parameters['active'];
		$this->locales = Locales::reconstitute($parameters['locales']);
	}
}
