<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Control\ExportForm\Callback;

use App\Application\DataProcessor\DataProcessFactory;

interface ExportCallbackInterface
{
    public function name(): string;

    public function __invoke(DataProcessFactory $dataProcessFactory, string $format, array $options): string;
}
