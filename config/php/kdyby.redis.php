<?php

declare(strict_types=1);

$redisConfig = array_filter([
	'host' => env('REDIS_HOST', NULL),
	'port' => env('REDIS_PORT|int', NULL),
	'auth' => env('REDIS_AUTH', NULL),
], static fn ($value): bool => $value !== NULL);

return 0 < count($redisConfig) ? ['kdyby.redis' => $redisConfig] : [];
