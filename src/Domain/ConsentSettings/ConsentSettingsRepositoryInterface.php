<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings;

use App\Domain\ConsentSettings\Exception\ConsentSettingsNotFoundException;
use App\Domain\ConsentSettings\ValueObject\ConsentSettingsId;

interface ConsentSettingsRepositoryInterface
{
    public function save(ConsentSettings $consentSettings): void;

    /**
     * @throws ConsentSettingsNotFoundException
     */
    public function get(ConsentSettingsId $id): ConsentSettings;
}
