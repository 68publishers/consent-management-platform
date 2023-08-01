<?php

declare(strict_types=1);

namespace App\Domain\Import;

use App\Domain\Import\Exception\ImportNotFoundException;
use App\Domain\Import\ValueObject\ImportId;

interface ImportRepositoryInterface
{
    public function save(Import $import): void;

    /**
     * @throws ImportNotFoundException
     */
    public function get(ImportId $id): Import;
}
