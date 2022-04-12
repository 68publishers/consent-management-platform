<?php

declare(strict_types=1);

$secure = $_ENV['COOKIE_SECURE'] ?? NULL;

if (NULL !== $secure) {
	if (in_array($secure, ['1', '0'], FALSE)) {
		$secure = (bool) $secure;
	}

	return [
		'http' => [
			'cookieSecure' => $secure,
		],
	];
}

return [];
