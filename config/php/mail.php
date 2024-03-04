<?php

declare(strict_types=1);

$mailConfig = array_filter([
    'host' => env('SMTP_HOST', null),
    'port' => env('SMTP_PORT|int', null),
    'username' => env('SMTP_USERNAME', null),
    'password' => env('SMTP_PASSWORD', null),
    'secure' => env('SMTP_SECURE', null),
], static fn ($value): bool => $value !== null);

return [
    'mail' => array_merge([
        'smtp' => env('SMTP_ENABLED|bool', false),
    ], $mailConfig),
];
