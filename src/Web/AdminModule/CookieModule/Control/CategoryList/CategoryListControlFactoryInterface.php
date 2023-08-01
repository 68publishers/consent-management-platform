<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CategoryList;

use App\Application\GlobalSettings\Locale;

interface CategoryListControlFactoryInterface
{
    public function create(?Locale $locale): CategoryListControl;
}
