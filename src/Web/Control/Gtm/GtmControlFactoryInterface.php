<?php

declare(strict_types=1);

namespace App\Web\Control\Gtm;

interface GtmControlFactoryInterface
{
    public function create(): GtmControl;
}
