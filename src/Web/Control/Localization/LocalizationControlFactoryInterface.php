<?php

declare(strict_types=1);

namespace App\Web\Control\Localization;

interface LocalizationControlFactoryInterface
{
    public function create(): LocalizationControl;
}
