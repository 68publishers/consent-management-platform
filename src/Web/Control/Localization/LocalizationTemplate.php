<?php

declare(strict_types=1);

namespace App\Web\Control\Localization;

use App\Application\Localization\Profiles;
use Nette\Bridges\ApplicationLatte\Template;

final class LocalizationTemplate extends Template
{
    public Profiles $profiles;
}
