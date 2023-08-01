<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CookieForm;

use App\ReadModel\Cookie\CookieView;
use Nette\Bridges\ApplicationLatte\Template;

final class CookieFormModalTemplate extends Template
{
    public ?CookieView $default = null;
}
