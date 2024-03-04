<?php

declare(strict_types=1);

$redisConfig = array_filter([
    'host' => env('REDIS_HOST', null),
    'port' => env('REDIS_PORT|int', null),
    'auth' => env('REDIS_AUTH|nullable', null),
], static fn ($value): bool => $value !== null);

$redisConfig['database'] = env('REDIS_DB_CACHE|int', 0);
$redisConfig['session'] = [
    'database' => env('REDIS_DB_SESSIONS|int', 1),
];

return 0 < count($redisConfig) ? ['kdyby.redis' => $redisConfig] : [];
