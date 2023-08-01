<?php

declare(strict_types=1);

$mailConfig = array_filter([
    'host' => env('SMTP_HOST', NULL),
    'port' => env('SMTP_PORT|int', NULL),
    'username' => env('SMTP_USERNAME', NULL),
    'password' => env('SMTP_PASSWORD', NULL),
    'secure' => env('SMTP_SECURE', NULL),
], static fn ($value): bool => $value !== NULL);

return [
    'mail' => array_merge([
        'smtp' => env('SMTP_ENABLED|bool', FALSE),
    ], $mailConfig),
];
