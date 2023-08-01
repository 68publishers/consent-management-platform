<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CategoryForm;

use App\ReadModel\Category\CategoryView;

interface CategoryFormControlFactoryInterface
{
    public function create(?CategoryView $default = null): CategoryFormControl;
}
