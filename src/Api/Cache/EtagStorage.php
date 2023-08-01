<?php

declare(strict_types=1);

namespace App\Api\Cache;

use Nette\Caching\Cache;
use Nette\Caching\Storage;
use Throwable;

final class EtagStorage implements EtagStoreInterface
{
    private Cache $cache;

    public function __construct(Storage $storage)
    {
        $this->cache = new Cache($storage, self::class);
    }

    /**
     * @throws Throwable
     */
    public function get(string $key): ?Etag
    {
        $etag = $this->cache->load($key);

        return $etag instanceof Etag ? $etag : null;
    }

    public function save(string $key, Etag $etag): void
    {
        $this->cache->save($key, $etag, [
            Cache::Expire => '1 day',
        ]);
    }

    public function remove(string $key): void
    {
        $this->cache->remove($key);
    }

    public function clear(): void
    {
        $this->cache->clean([
            Cache::All => true,
        ]);
    }
}
