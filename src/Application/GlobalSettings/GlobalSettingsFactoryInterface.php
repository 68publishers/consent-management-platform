<?php

declare(strict_types=1);

namespace App\Application\GlobalSettings;

interface GlobalSettingsFactoryInterface
{
    public function create(): GlobalSettingsInterface;
}
