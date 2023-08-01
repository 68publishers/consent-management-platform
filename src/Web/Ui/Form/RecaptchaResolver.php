<?php

declare(strict_types=1);

namespace App\Web\Ui\Form;

final class RecaptchaResolver
{
    public function __construct(
        private readonly bool $isEnabled,
    ) {}

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }
}
