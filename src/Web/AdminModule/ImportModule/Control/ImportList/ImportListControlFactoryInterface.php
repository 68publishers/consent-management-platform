<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportList;

interface ImportListControlFactoryInterface
{
    public function create(): ImportListControl;
}
