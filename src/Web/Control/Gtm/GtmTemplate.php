<?php

declare(strict_types=1);

namespace App\Web\Control\Gtm;

use Nette\Bridges\ApplicationLatte\Template;

final class GtmTemplate extends Template
{
    public ?string $containerId;
}
