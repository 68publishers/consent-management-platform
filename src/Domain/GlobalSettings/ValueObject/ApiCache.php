<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\ValueObject;

final class ApiCache
{
    private array $cacheControlDirectives = [];

    private bool $useEntityTag = false;

    private function __construct() {}

    /**
     * @return static
     */
    public static function create(array $cacheControlDirectives = [], bool $useEntityTag = false): self
    {
        $apiCache = new self();
        $apiCache->cacheControlDirectives = $cacheControlDirectives;
        $apiCache->useEntityTag = $useEntityTag;

        return $apiCache;
    }

    public function cacheControlDirectives(): array
    {
        return $this->cacheControlDirectives;
    }

    public function cacheControlHeader(): ?string
    {
        return empty($this->cacheControlDirectives) ? null : implode(', ', $this->cacheControlDirectives);
    }

    public function useEntityTag(): bool
    {
        return $this->useEntityTag;
    }

    /**
     * @return $this
     */
    public function withUseEntityTag(bool $useEntityTag = true): self
    {
        $apiCache = clone $this;
        $apiCache->useEntityTag = $useEntityTag;

        return $apiCache;
    }

    /**
     * @return $this
     */
    public function withCacheControlDirectives(string ...$directives): self
    {
        $apiCache = clone $this;
        $apiCache->cacheControlDirectives = array_unique(array_merge($this->cacheControlDirectives, $directives));

        return $apiCache;
    }

    public function equals(self $apiCache): bool
    {
        if ($this->useEntityTag() !== $apiCache->useEntityTag()) {
            return false;
        }

        if (count($this->cacheControlDirectives()) !== count($apiCache->cacheControlDirectives())) {
            return false;
        }

        $directives = $apiCache->cacheControlDirectives();

        foreach ($this->cacheControlDirectives() as $directive) {
            if (!in_array($directive, $directives, true)) {
                return false;
            }
        }

        return true;
    }
}
