<?php

declare(strict_types=1);

$recaptchaConfig = array_filter([
    'siteKey' => env('GOOGLE_RECAPTCHA_SITE_KEY', ''),
    'secretKey' => env('GOOGLE_RECAPTCHA_SECRET_KEY', ''),
], static fn ($value): bool => $value !== NULL);

return 0 < count($recaptchaConfig) ? ['contributte.recaptcha' => $recaptchaConfig] : [];
