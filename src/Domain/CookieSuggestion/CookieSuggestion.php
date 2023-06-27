<?php

declare(strict_types=1);

namespace App\Domain\CookieSuggestion;

use Exception;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use App\Domain\Project\ValueObject\ProjectId;
use Doctrine\Common\Collections\ArrayCollection;
use App\Domain\CookieSuggestion\ValueObject\Name;
use App\Domain\CookieSuggestion\ValueObject\Domain;
use App\Domain\CookieSuggestion\ValueObject\FoundOnUrl;
use App\Domain\CookieSuggestion\ValueObject\ScenarioName;
use App\Domain\CookieSuggestion\Event\CookieOccurrenceAdded;
use App\Domain\CookieSuggestion\Event\CookieSuggestionCreated;
use App\Domain\CookieSuggestion\ValueObject\AcceptedCategories;
use App\Domain\CookieSuggestion\ValueObject\CookieOccurrenceId;
use App\Domain\CookieSuggestion\ValueObject\CookieSuggestionId;
use App\Domain\CookieSuggestion\Command\CreateCookieSuggestionCommand;
use App\Domain\CookieSuggestion\Event\CookieOccurrenceFoundOnUrlChanged;
use App\Domain\CookieSuggestion\Event\CookieOccurrenceLastFoundAtChanged;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;
use App\Domain\CookieSuggestion\Event\CookieOccurrenceAcceptedCategoriesChanged;
use App\Domain\CookieSuggestion\Event\CookieSuggestionIgnoredPermanentlyChanged;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootTrait;
use App\Domain\CookieSuggestion\Command\CookieOccurrence as CommandCookieOccurrence;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootInterface;
use App\Domain\CookieSuggestion\Event\CookieSuggestionIgnoredUntilNextOccurrenceChanged;

final class CookieSuggestion implements AggregateRootInterface
{
	use AggregateRootTrait;

	private CookieSuggestionId $id;

	private ProjectId $projectId;

	private DateTimeImmutable $createdAt;

	private Name $name;

	private Domain $domain;

	private bool $ignoredUntilNextOccurrence;

	private bool $ignoredPermanently;

	private Collection $occurrences;

	/**
	 * @throws Exception
	 */
	public static function create(
		CreateCookieSuggestionCommand $command,
		CheckSuggestionNameAndDomainUniquenessInterface $checkSuggestionNameAndDomainUniqueness
	): self {
		$cookieSuggestion = new self();

		$id = NULL !== $command->cookieSuggestionId() ? CookieSuggestionId::fromString($command->cookieSuggestionId()) : CookieSuggestionId::new();
		$projectId = ProjectId::fromString($command->projectId());
		$name = Name::fromValue($command->name());
		$domain = Domain::fromValue($command->domain());

		$checkSuggestionNameAndDomainUniqueness($id, $projectId, $name, $domain);

		$cookieSuggestion->recordThat(CookieSuggestionCreated::create($id, $projectId, $name, $domain));

		foreach ($command->occurrences() as $occurrence) {
			$cookieSuggestion->addOccurrence($occurrence);
		}

		return $cookieSuggestion;
	}

	/**
	 * @throws Exception
	 */
	public function addOccurrence(CommandCookieOccurrence $addedOccurrence): void
	{
		$scenarioName = ScenarioName::fromValue($addedOccurrence->scenarioName);
		$foundOnUrl = FoundOnUrl::fromValue($addedOccurrence->foundOnUrl);
		$acceptedCategories = AcceptedCategories::reconstitute($addedOccurrence->acceptedCategories);
		$lastFoundAt = new DateTimeImmutable($addedOccurrence->lastFoundAt);

		$existing = $this->occurrences->filter(static fn (CookieOccurrence $item): bool => $item->scenarioName()->equals($scenarioName))->first();

		if (!$existing instanceof CookieOccurrence) {
			$this->recordThat(CookieOccurrenceAdded::create(
				$this->id,
				CookieOccurrenceId::new(),
				$scenarioName,
				$foundOnUrl,
				$acceptedCategories,
				$lastFoundAt,
			));

			return;
		}

		if (!$existing->foundOnUrl()->equals($foundOnUrl)) {
			$this->recordThat(CookieOccurrenceFoundOnUrlChanged::create($this->id, $existing->id(), $foundOnUrl));
		}

		if (!$existing->acceptedCategories()->equals($acceptedCategories)) {
			$this->recordThat(CookieOccurrenceAcceptedCategoriesChanged::create($this->id, $existing->id(), $acceptedCategories));
		}

		if ($existing->lastFoundAt()->getTimestamp() !== $lastFoundAt->getTimestamp()) {
			$this->recordThat(CookieOccurrenceLastFoundAtChanged::create($this->id, $existing->id(), $lastFoundAt));
		}
	}

	public function ignoreUntilNextOccurrence(): void
	{
		if (FALSE === $this->ignoredUntilNextOccurrence) {
			$this->recordThat(CookieSuggestionIgnoredUntilNextOccurrenceChanged::create($this->id, TRUE));
		}
	}

	public function ignorePermanently(): void
	{
		if (FALSE === $this->ignoredPermanently) {
			$this->recordThat(CookieSuggestionIgnoredPermanentlyChanged::create($this->id, TRUE));
		}
	}

	public function doNotIgnore(): void
	{
		if (TRUE === $this->ignoredUntilNextOccurrence) {
			$this->recordThat(CookieSuggestionIgnoredUntilNextOccurrenceChanged::create($this->id, FALSE));
		}

		if (TRUE === $this->ignoredPermanently) {
			$this->recordThat(CookieSuggestionIgnoredPermanentlyChanged::create($this->id, FALSE));
		}
	}

	public function aggregateId(): AggregateId
	{
		return AggregateId::fromUuid($this->id->id());
	}

	protected function whenCookieSuggestionCreated(CookieSuggestionCreated $event): void
	{
		$this->id = $event->cookieSuggestionId();
		$this->projectId = $event->projectId();
		$this->createdAt = $event->createdAt();
		$this->name = $event->name();
		$this->domain = $event->domain();
		$this->ignoredUntilNextOccurrence = FALSE;
		$this->occurrences = new ArrayCollection();
	}

	protected function whenCookieOccurrenceAdded(CookieOccurrenceAdded $event): void
	{
		$this->occurrences->add(new CookieOccurrence(
			$this,
			$event->cookieOccurrenceId(),
			$event->scenarioName(),
			$event->foundOnUrl(),
			$event->acceptedCategories(),
			$event->lastFoundAt(),
		));
		$this->ignoredUntilNextOccurrence = FALSE;
	}

	protected function whenCookieOccurrenceFoundOnUrlChanged(CookieOccurrenceFoundOnUrlChanged $event): void
	{
		$existing = $this->occurrences->filter(static fn (CookieOccurrence $item): bool => $item->id()->equals($event->cookieOccurrenceId()))->first();

		if ($existing instanceof CookieOccurrence) {
			$existing->setFoundOnUrl($event->foundOnUrl());
		}
		$this->ignoredUntilNextOccurrence = FALSE;
	}

	protected function whenCookieOccurrenceAcceptedCategoriesChanged(CookieOccurrenceAcceptedCategoriesChanged $event): void
	{
		$existing = $this->occurrences->filter(static fn (CookieOccurrence $item): bool => $item->id()->equals($event->cookieOccurrenceId()))->first();

		if ($existing instanceof CookieOccurrence) {
			$existing->setAcceptedCategories($event->acceptedCategories());
		}
		$this->ignoredUntilNextOccurrence = FALSE;
	}

	protected function whenCookieOccurrenceLastFoundAtChanged(CookieOccurrenceLastFoundAtChanged $event): void
	{
		$existing = $this->occurrences->filter(static fn (CookieOccurrence $item): bool => $item->id()->equals($event->cookieOccurrenceId()))->first();

		if ($existing instanceof CookieOccurrence) {
			$existing->setLastFoundAt($event->lastFoundAt());
		}
		$this->ignoredUntilNextOccurrence = FALSE;
	}

	protected function whenCookieSuggestionIgnoredUntilNextOccurrenceChanged(CookieSuggestionIgnoredUntilNextOccurrenceChanged $event): void
	{
		$this->ignoredUntilNextOccurrence = $event->ignoredUntilNextOccurrence();
	}

	protected function whenCookieSuggestionIgnoredPermanentlyChanged(CookieSuggestionIgnoredPermanentlyChanged $event): void
	{
		$this->ignoredPermanently = $event->ignoredPermanently();
	}
}
