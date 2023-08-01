<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Presenter;

use App\Application\GlobalSettings\Locale;
use App\ReadModel\User\UserView;
use App\Web\Ui\DefaultPresenterTemplate;

abstract class AdminTemplate extends DefaultPresenterTemplate
{
    public UserView $identity;

    /** @var array<Locale> */
    public array $locales;

    public ?Locale $defaultLocale = null;
}
