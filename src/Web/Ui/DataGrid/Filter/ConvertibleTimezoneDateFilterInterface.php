<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\Filter;

interface ConvertibleTimezoneDateFilterInterface
{
    public function getTimezoneFrom(): string;

    public function getTimezoneTo(): string;
}
