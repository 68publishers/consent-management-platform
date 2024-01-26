<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\ExternalAuthList;

interface ExternalAuthListControlFactoryInterface
{
    public function create(string $userId): ExternalAuthListControl;
}
