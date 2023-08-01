<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Event;

use App\Domain\CookieProvider\ValueObject\Code;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\CookieProvider\ValueObject\Link;
use App\Domain\CookieProvider\ValueObject\Name;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use App\Domain\CookieProvider\ValueObject\Purpose;
use SixtyEightPublishers\ArchitectureBundle\Domain\Event\AbstractDomainEvent;

final class CookieProviderCreated extends AbstractDomainEvent
{
    private CookieProviderId $cookieProviderId;

    private Code $code;

    private ProviderType $type;

    private Name $name;

    private Link $link;

    private array $purposes;

    private bool $private;

    private bool $active;

    /**
     * @param Purpose[] $purposes
     *
     * @return static
     */
    public static function create(CookieProviderId $cookieProviderId, Code $code, ProviderType $type, Name $name, Link $link, array $purposes, bool $private, bool $active): self
    {
        $event = self::occur($cookieProviderId->toString(), [
            'code' => $code->value(),
            'type' => $type->value(),
            'name' => $name->value(),
            'link' => $link->value(),
            'purposes' => array_map(static fn (Purpose $purpose): string => $purpose->value(), $purposes),
            'private' => $private,
            'active' => $active,
        ]);

        $event->cookieProviderId = $cookieProviderId;
        $event->code = $code;
        $event->type = $type;
        $event->name = $name;
        $event->link = $link;
        $event->purposes = $purposes;
        $event->private = $private;
        $event->active = $active;

        return $event;
    }

    public function cookieProviderId(): CookieProviderId
    {
        return $this->cookieProviderId;
    }

    public function code(): Code
    {
        return $this->code;
    }

    public function type(): ProviderType
    {
        return $this->type;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function link(): Link
    {
        return $this->link;
    }

    /**
     * @return Purpose[]
     */
    public function purposes(): array
    {
        return $this->purposes;
    }

    public function private(): bool
    {
        return $this->private;
    }

    public function active(): bool
    {
        return $this->active;
    }

    protected function reconstituteState(array $parameters): void
    {
        $this->cookieProviderId = CookieProviderId::fromUuid($this->aggregateId()->id());
        $this->code = Code::fromValue($parameters['code']);
        $this->type = ProviderType::fromValue($parameters['type']);
        $this->name = Name::fromValue($parameters['name']);
        $this->link = Link::fromValue($parameters['link']);
        $this->purposes = array_map(static fn (string $purpose): Purpose => Purpose::fromValue($purpose), $parameters['purposes']);
        $this->private = (bool) $parameters['private'];
        $this->active = (bool) $parameters['active'];
    }
}
