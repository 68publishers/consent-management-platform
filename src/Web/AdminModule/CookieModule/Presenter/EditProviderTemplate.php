<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\ReadModel\CookieProvider\CookieProviderView;
use App\ReadModel\Project\ProjectView;
use App\Web\AdminModule\Presenter\AdminTemplate;

final class EditProviderTemplate extends AdminTemplate
{
    public CookieProviderView $cookieProviderView;

    public ?ProjectView $projectView = null;
}
