<?php

declare(strict_types=1);

namespace App\Domain\Consent;

use App\Domain\Consent\Exception\ConsentNotFoundException;
use App\Domain\Consent\ValueObject\ConsentId;

interface ConsentRepositoryInterface
{
    public function save(Consent $consent): void;

    /**
     * @throws ConsentNotFoundException
     */
    public function get(ConsentId $id): Consent;
}
