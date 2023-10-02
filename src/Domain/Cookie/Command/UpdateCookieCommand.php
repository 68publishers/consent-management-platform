<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class UpdateCookieCommand extends AbstractCommand
{
    public static function create(string $cookieId): self
    {
        return self::fromParameters([
            'cookie_id' => $cookieId,
        ]);
    }

    public function cookieId(): string
    {
        return $this->getParam('cookie_id');
    }

    public function categoryId(): ?string
    {
        return $this->getParam('category_id');
    }

    public function name(): ?string
    {
        return $this->getParam('name');
    }

    public function domain(): ?string
    {
        return $this->getParam('domain');
    }

    /**
     * @return array<string>|null
     */
    public function purposes(): ?array
    {
        return $this->getParam('purposes');
    }

    public function processingTime(): ?string
    {
        return $this->getParam('processing_time');
    }

    public function active(): ?bool
    {
        return $this->getParam('active');
    }

    /**
     * @return bool|array<int, string|null>|null
     */
    public function environments(): bool|array|null
    {
        return $this->getParam('environments');
    }

    public function withCategoryId(string $categoryId): self
    {
        return $this->withParam('category_id', $categoryId);
    }

    public function withName(string $name): self
    {
        return $this->withParam('name', $name);
    }

    public function withDomain(string $domain): self
    {
        return $this->withParam('domain', $domain);
    }

    /**
     * @param array<string> $purposes
     */
    public function withPurposes(array $purposes): self
    {
        return $this->withParam('purposes', $purposes);
    }

    public function withProcessingTime(string $processingTime): self
    {
        return $this->withParam('processing_time', $processingTime);
    }

    public function withActive(bool $active): self
    {
        return $this->withParam('active', $active);
    }

    /**
     * @param bool|array<int, string|null> $environments
     */
    public function withEnvironments(bool|array $environments): self
    {
        return $this->withParam('environments', $environments);
    }
}
