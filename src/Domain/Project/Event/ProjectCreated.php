<?php

declare(strict_types=1);

namespace App\Domain\Project\Event;

use DateTimeZone;
use App\Domain\Project\ValueObject\Code;
use App\Domain\Project\ValueObject\Name;
use App\Domain\Project\ValueObject\Color;
use App\Domain\Shared\ValueObject\Locale;
use App\Domain\Shared\ValueObject\Locales;
use App\Domain\Project\ValueObject\ProjectId;
use App\Domain\Project\ValueObject\Description;
use App\Domain\Shared\ValueObject\LocalesConfig;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class ProjectCreated extends AbstractDomainEvent
{
	private ProjectId $projectId;

	private CookieProviderId $cookieProviderId;

	private Name $name;

	private Code $code;

	private Description $description;

	private Color $color;

	private bool $active;

	private LocalesConfig $locales;

	private DateTimeZone $timezone;

	/**
	 * @param \App\Domain\Project\ValueObject\ProjectId               $projectId
	 * @param \App\Domain\CookieProvider\ValueObject\CookieProviderId $cookieProviderId
	 * @param \App\Domain\Project\ValueObject\Name                    $name
	 * @param \App\Domain\Project\ValueObject\Code                    $code
	 * @param \App\Domain\Project\ValueObject\Description             $description
	 * @param \App\Domain\Project\ValueObject\Color                   $color
	 * @param bool                                                    $active
	 * @param \App\Domain\Shared\ValueObject\LocalesConfig            $locales
	 * @param \DateTimeZone                                           $timezone
	 *
	 * @return static
	 */
	public static function create(ProjectId $projectId, CookieProviderId $cookieProviderId, Name $name, Code $code, Description $description, Color $color, bool $active, LocalesConfig $locales, DateTimeZone $timezone): self
	{
		$event = self::occur($projectId->toString(), [
			'cookie_provider_id' => $cookieProviderId->toString(),
			'name' => $name->value(),
			'code' => $code->value(),
			'description' => $description->value(),
			'color' => $color->value(),
			'active' => $active,
			'locales' => $locales->locales()->toArray(),
			'default_locale' => $locales->defaultLocale()->value(),
			'timezone' => $timezone->getName(),
		]);

		$event->projectId = $projectId;
		$event->cookieProviderId = $cookieProviderId;
		$event->name = $name;
		$event->code = $code;
		$event->description = $description;
		$event->color = $color;
		$event->active = $active;
		$event->locales = $locales;
		$event->timezone = $timezone;

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
	 * @return \App\Domain\CookieProvider\ValueObject\CookieProviderId
	 */
	public function cookieProviderId(): CookieProviderId
	{
		return $this->cookieProviderId;
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
	 * @return \App\Domain\Shared\ValueObject\LocalesConfig
	 */
	public function locales(): LocalesConfig
	{
		return $this->locales;
	}

	/**
	 * @return \DateTimeZone
	 */
	public function timezone(): DateTimeZone
	{
		return $this->timezone;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function reconstituteState(array $parameters): void
	{
		$this->projectId = ProjectId::fromUuid($this->aggregateId()->id());
		$this->cookieProviderId = CookieProviderId::fromString($parameters['cookie_provider_id']);
		$this->name = Name::fromValue($parameters['name']);
		$this->code = Code::fromValue($parameters['code']);
		$this->description = Description::fromValue($parameters['description']);
		$this->color = Color::fromValue($parameters['color']);
		$this->active = (bool) $parameters['active'];
		$this->locales = LocalesConfig::create(Locales::reconstitute($parameters['locales']), Locale::fromValue($parameters['default_locale']));
		$this->timezone = new DateTimeZone($parameters['timezone']);
	}
}
