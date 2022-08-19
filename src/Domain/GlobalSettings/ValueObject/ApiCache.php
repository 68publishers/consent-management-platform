<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings\ValueObject;

final class ApiCache
{
	private array $cacheControlDirectives = [];

	private bool $useEntityTag = FALSE;

	private function __construct()
	{
	}

	/**
	 * @param array $cacheControlDirectives
	 * @param bool  $useEntityTag
	 *
	 * @return static
	 */
	public static function create(array $cacheControlDirectives = [], bool $useEntityTag = FALSE): self
	{
		$apiCache = new self();
		$apiCache->cacheControlDirectives = $cacheControlDirectives;
		$apiCache->useEntityTag = $useEntityTag;

		return $apiCache;
	}

	/**
	 * @return array
	 */
	public function cacheControlDirectives(): array
	{
		return $this->cacheControlDirectives;
	}

	/**
	 * @return string
	 */
	public function cacheControlHeader(): ?string
	{
		return empty($this->cacheControlDirectives) ? NULL : implode(', ', $this->cacheControlDirectives);
	}

	/**
	 * @return bool
	 */
	public function useEntityTag(): bool
	{
		return $this->useEntityTag;
	}

	/**
	 * @param bool $useEntityTag
	 *
	 * @return $this
	 */
	public function withUseEntityTag(bool $useEntityTag = TRUE): self
	{
		$apiCache = clone $this;
		$apiCache->useEntityTag = $useEntityTag;

		return $apiCache;
	}

	/**
	 * @param string ...$directives
	 *
	 * @return $this
	 */
	public function withCacheControlDirectives(string ...$directives): self
	{
		$apiCache = clone $this;
		$apiCache->cacheControlDirectives = array_unique(array_merge($this->cacheControlDirectives, $directives));

		return $apiCache;
	}

	/**
	 * @param \App\Domain\GlobalSettings\ValueObject\ApiCache $apiCache
	 *
	 * @return bool
	 */
	public function equals(self $apiCache): bool
	{
		if ($this->useEntityTag() !== $apiCache->useEntityTag()) {
			return FALSE;
		}

		if (count($this->cacheControlDirectives()) !== count($apiCache->cacheControlDirectives())) {
			return FALSE;
		}

		$directives = $apiCache->cacheControlDirectives();

		foreach ($this->cacheControlDirectives() as $directive) {
			if (!in_array($directive, $directives, TRUE)) {
				return FALSE;
			}
		}

		return TRUE;
	}
}
