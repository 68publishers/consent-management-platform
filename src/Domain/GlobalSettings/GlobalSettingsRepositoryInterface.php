<?php

declare(strict_types=1);

namespace App\Domain\GlobalSettings;

use App\Domain\ConsentSettings\ConsentSettings;

interface GlobalSettingsRepositoryInterface
{
    public function save(GlobalSettings $globalSettings): void;

    /**
     * Singleton!
     *
     * @return ConsentSettings|NULL
     */
    public function get(): ?GlobalSettings;
}
