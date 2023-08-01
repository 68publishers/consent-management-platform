<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Application\DataProcessor\Read\Reader\ReaderInterface;

interface RunnerInterface
{
    public function run(ReaderInterface $reader, ImportOptions $options): ImportState;
}
