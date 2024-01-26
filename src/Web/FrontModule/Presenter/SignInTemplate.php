<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Presenter;

use App\Web\Ui\DefaultPresenterTemplate;

final class SignInTemplate extends DefaultPresenterTemplate
{
    public ?string $backLink = null;

    /** @var array<int, string> */
    public array $enabledOauthTypes;
}
