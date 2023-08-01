<?php

declare(strict_types=1);

namespace App\Api\Cache;

interface EtagStoreInterface
{
    public function get(string $key): ?Etag;

    public function save(string $key, Etag $etag): void;

    public function remove(string $key): void;

    public function clear(): void;
}
