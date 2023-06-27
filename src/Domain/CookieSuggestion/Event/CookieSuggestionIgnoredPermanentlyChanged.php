<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion\Event;

use App\Domain\CookieSuggestion\ValueObject\CookieSuggestionId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieSuggestionIgnoredPermanentlyChanged extends AbstractDomainEvent
{
	private CookieSuggestionId $cookieSuggestionId;

	private bool $ignoredPermanently;

	public static function create(
		CookieSuggestionId $cookieSuggestionId,
		bool $ignoredPermanently
	): self {
		$event = self::occur($cookieSuggestionId->toString(), [
			'ignored_permanently' => $ignoredPermanently,
		]);

		$event->cookieSuggestionId = $cookieSuggestionId;
		$event->ignoredPermanently = $ignoredPermanently;

		return $event;
	}

	public function cookieSuggestionId(): CookieSuggestionId
	{
		return $this->cookieSuggestionId;
	}

	public function ignoredPermanently(): bool
	{
		return $this->ignoredPermanently;
	}

	protected function reconstituteState(array $parameters): void
	{
		$this->cookieSuggestionId = CookieSuggestionId::fromUuid($this->aggregateId()->id());
		$this->ignoredPermanently = (bool) $parameters['ignored_permanently'];
	}
}
