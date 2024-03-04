<?php

declare(strict_types=1);

$secure = $_ENV['COOKIE_SECURE'] ?? null;

if (null !== $secure) {
    if (in_array($secure, ['1', '0'], false)) {
        $secure = (bool) $secure;
    }

    return [
        'http' => [
            'cookieSecure' => $secure,
        ],
    ];
}

return [];
