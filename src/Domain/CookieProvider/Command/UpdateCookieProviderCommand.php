<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class UpdateCookieProviderCommand extends AbstractCommand
{
    /**
     * @return static
     */
    public static function create(string $cookieProviderId): self
    {
        return self::fromParameters([
            'cookie_provider_id' => $cookieProviderId,
        ]);
    }

    public function cookieProviderId(): string
    {
        return $this->getParam('cookie_provider_id');
    }

    public function code(): ?string
    {
        return $this->getParam('code');
    }

    public function type(): ?string
    {
        return $this->getParam('type');
    }

    public function name(): ?string
    {
        return $this->getParam('name');
    }

    public function link(): ?string
    {
        return $this->getParam('link');
    }

    public function active(): ?bool
    {
        return $this->getParam('active');
    }

    public function purposes(): ?array
    {
        return $this->getParam('purposes');
    }

    /**
     * @return $this
     */
    public function withCode(string $code): self
    {
        return $this->withParam('code', $code);
    }

    /**
     * @return $this
     */
    public function withType(string $type): self
    {
        return $this->withParam('type', $type);
    }

    /**
     * @return $this
     */
    public function withName(string $name): self
    {
        return $this->withParam('name', $name);
    }
    /**
     * @return $this
     */
    public function withLink(string $link): self
    {
        return $this->withParam('link', $link);
    }

    /**
     * @return $this
     */
    public function withActive(bool $active): self
    {
        return $this->withParam('active', $active);
    }

    /**
     * @param string[] $purposes
     *
     * @return $this
     */
    public function withPurposes(array $purposes): self
    {
        return $this->withParam('purposes', $purposes);
    }
}
