<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider;

use DateTimeImmutable;
use App\Domain\Shared\ValueObject\Locale;
use Doctrine\Common\Collections\Collection;
use App\Domain\CookieProvider\ValueObject\Code;
use App\Domain\CookieProvider\ValueObject\Link;
use App\Domain\CookieProvider\ValueObject\Name;
use Doctrine\Common\Collections\ArrayCollection;
use App\Domain\CookieProvider\ValueObject\Purpose;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use App\Domain\CookieProvider\Event\CookieProviderCreated;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\CookieProvider\Event\CookieProviderCodeChanged;
use App\Domain\CookieProvider\Event\CookieProviderLinkChanged;
use App\Domain\CookieProvider\Event\CookieProviderNameChanged;
use App\Domain\CookieProvider\Event\CookieProviderTypeChanged;
use App\Domain\CookieProvider\Event\CookieProviderPurposeChanged;
use App\Domain\CookieProvider\Command\CreateCookieProviderCommand;
use App\Domain\CookieProvider\Command\UpdateCookieProviderCommand;
use App\Domain\CookieProvider\Event\CookieProviderActiveStateChanged;
use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AggregateId;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\AggregateRootInterface;
use SixtyEightPublishers\ArchitectureBundle\Domain\Aggregate\DeletableAggregateRootTrait;

final class CookieProvider implements AggregateRootInterface
{
	use DeletableAggregateRootTrait;

	private CookieProviderId $id;

	private DateTimeImmutable $createdAt;

	private Code $code;

	private ProviderType $type;

	private Name $name;

	private Link $link;

	private bool $private;

	private bool $active;

	private Collection $translations;

	/**
	 * @param \App\Domain\CookieProvider\Command\CreateCookieProviderCommand $command
	 * @param \App\Domain\CookieProvider\CheckCodeUniquenessInterface        $checkCodeUniqueness
	 *
	 * @return static
	 */
	public static function create(CreateCookieProviderCommand $command, CheckCodeUniquenessInterface $checkCodeUniqueness): self
	{
		$cookieProvider = new self();

		$id = NULL !== $command->cookieProviderId() ? CookieProviderId::fromString($command->cookieProviderId()) : CookieProviderId::new();
		$code = Code::withValidation($command->code());
		$type = ProviderType::fromValue($command->type());
		$name = Name::fromValue($command->name());
		$link = Link::withValidation($command->link());
		$purposes = array_map(static fn (string $purpose): Purpose => Purpose::fromValue($purpose), $command->purposes());

		$checkCodeUniqueness($id, $code);

		$cookieProvider->recordThat(CookieProviderCreated::create($id, $code, $type, $name, $link, $purposes, $command->private(), $command->active()));

		return $cookieProvider;
	}

	/**
	 * @param \App\Domain\CookieProvider\Command\UpdateCookieProviderCommand $command
	 * @param \App\Domain\CookieProvider\CheckCodeUniquenessInterface        $checkCodeUniqueness
	 *
	 * @return void
	 */
	public function update(UpdateCookieProviderCommand $command, CheckCodeUniquenessInterface $checkCodeUniqueness): void
	{
		if (NULL !== $command->code()) {
			$this->changeCode(Code::withValidation($command->code()), $checkCodeUniqueness);
		}

		if (NULL !== $command->type()) {
			$this->changeType(ProviderType::fromValue($command->type()));
		}

		if (NULL !== $command->name()) {
			$this->changeName(Name::fromValue($command->name()));
		}

		if (NULL !== $command->type()) {
			$this->changeType(ProviderType::fromValue($command->type()));
		}

		if (NULL !== $command->link()) {
			$this->changeLink(Link::withValidation($command->link()));
		}

		if (NULL !== $command->active()) {
			$this->changeActiveState($command->active());
		}

		if (NULL !== $command->purposes()) {
			foreach ($command->purposes() as $locale => $purpose) {
				$this->changePurpose(Locale::fromValue($locale), Purpose::fromValue($purpose));
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function aggregateId(): AggregateId
	{
		return AggregateId::fromUuid($this->id->id());
	}

	/**
	 * @param \App\Domain\CookieProvider\ValueObject\Code             $code
	 * @param \App\Domain\CookieProvider\CheckCodeUniquenessInterface $checkCodeUniqueness
	 *
	 * @return void
	 */
	public function changeCode(Code $code, CheckCodeUniquenessInterface $checkCodeUniqueness): void
	{
		if (!$this->code->equals($code)) {
			$checkCodeUniqueness($this->id, $code);

			$this->recordThat(CookieProviderCodeChanged::create($this->id, $code));
		}
	}

	/**
	 * @param \App\Domain\CookieProvider\ValueObject\ProviderType $type
	 *
	 * @return void
	 */
	public function changeType(ProviderType $type): void
	{
		if (!$this->type->equals($type)) {
			$this->recordThat(CookieProviderTypeChanged::create($this->id, $type));
		}
	}

	/**
	 * @param \App\Domain\CookieProvider\ValueObject\Name $name
	 *
	 * @return void
	 */
	public function changeName(Name $name): void
	{
		if (!$this->name->equals($name)) {
			$this->recordThat(CookieProviderNameChanged::create($this->id, $name));
		}
	}

	/**
	 * @param \App\Domain\CookieProvider\ValueObject\Link $link
	 *
	 * @return void
	 */
	public function changeLink(Link $link): void
	{
		if (!$this->link->equals($link)) {
			$this->recordThat(CookieProviderLinkChanged::create($this->id, $link));
		}
	}

	/**
	 * @param bool $active
	 *
	 * @return void
	 */
	public function changeActiveState(bool $active): void
	{
		if ($this->active !== $active) {
			$this->recordThat(CookieProviderActiveStateChanged::create($this->id, $active));
		}
	}

	/**
	 * @param \App\Domain\Shared\ValueObject\Locale          $locale
	 * @param \App\Domain\CookieProvider\ValueObject\Purpose $purpose
	 *
	 * @return void
	 */
	public function changePurpose(Locale $locale, Purpose $purpose): void
	{
		$translation = $this->translations->filter(static fn (CookieProviderTranslation $translation): bool => $translation->locale()->equals($locale))->first();

		if (!$translation instanceof CookieProviderTranslation || !$translation->purpose()->equals($purpose)) {
			$this->recordThat(CookieProviderPurposeChanged::create($this->id, $locale, $purpose));
		}
	}

	/**
	 * @param \App\Domain\CookieProvider\Event\CookieProviderCreated $event
	 *
	 * @return void
	 */
	protected function whenCookieProviderCreated(CookieProviderCreated $event): void
	{
		$this->id = $event->cookieProviderId();
		$this->createdAt = $event->createdAt();
		$this->code = $event->code();
		$this->type = $event->type();
		$this->name = $event->name();
		$this->link = $event->link();
		$this->private = $event->private();
		$this->active = $event->active();
		$this->translations = new ArrayCollection();

		foreach ($event->purposes() as $locale => $purpose) {
			$this->translations->add(CookieProviderTranslation::create($this, Locale::fromValue($locale), $purpose));
		}
	}

	/**
	 * @param \App\Domain\CookieProvider\Event\CookieProviderCodeChanged $event
	 *
	 * @return void
	 */
	protected function whenCookieProviderCodeChanged(CookieProviderCodeChanged $event): void
	{
		$this->code = $event->code();
	}

	/**
	 * @param \App\Domain\CookieProvider\Event\CookieProviderTypeChanged $event
	 *
	 * @return void
	 */
	protected function whenCookieProviderTypeChanged(CookieProviderTypeChanged $event): void
	{
		$this->type = $event->type();
	}

	/**
	 * @param \App\Domain\CookieProvider\Event\CookieProviderNameChanged $event
	 *
	 * @return void
	 */
	protected function whenCookieProviderNameChanged(CookieProviderNameChanged $event): void
	{
		$this->name = $event->name();
	}

	/**
	 * @param \App\Domain\CookieProvider\Event\CookieProviderLinkChanged $event
	 *
	 * @return void
	 */
	protected function whenCookieProviderLinkChanged(CookieProviderLinkChanged $event): void
	{
		$this->link = $event->link();
	}

	/**
	 * @param \App\Domain\CookieProvider\Event\CookieProviderActiveStateChanged $event
	 *
	 * @return void
	 */
	protected function whenCookieProviderActiveStateChanged(CookieProviderActiveStateChanged $event): void
	{
		$this->active = $event->active();
	}

	/**
	 * @param \App\Domain\CookieProvider\Event\CookieProviderPurposeChanged $event
	 *
	 * @return void
	 */
	protected function whenCookieProviderPurposeChanged(CookieProviderPurposeChanged $event): void
	{
		$translation = $this->translations->filter(static fn (CookieProviderTranslation $translation): bool => $translation->locale()->equals($event->locale()))->first();

		if ($translation instanceof CookieProviderTranslation) {
			$translation->setPurpose($event->purpose());

			return;
		}

		$this->translations->add(CookieProviderTranslation::create($this, $event->locale(), $event->purpose()));
	}
}
