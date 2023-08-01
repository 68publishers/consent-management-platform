<?php

declare(strict_types=1);

namespace App\Web\Control\Footer;

interface FooterControlFactoryInterface
{
    public function create(): FooterControl;
}
