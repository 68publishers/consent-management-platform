<?php

declare(strict_types=1);

namespace App\Api\Cache;

interface EtagStoreInterface
{
	/**
	 * @param string $key
	 *
	 * @return \App\Api\Cache\Etag|NULL
	 */
	public function get(string $key): ?Etag;

	/**
	 * @param string              $key
	 * @param \App\Api\Cache\Etag $etag
	 *
	 * @return void
	 */
	public function save(string $key, Etag $etag): void;

	/**
	 * @param string $key
	 *
	 * @return void
	 */
	public function remove(string $key): void;

	/**
	 * @return void
	 */
	public function clear(): void;
}
