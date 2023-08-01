<?php

declare(strict_types=1);

namespace App\Web\Ui;

use Nette\Bridges\ApplicationLatte\Template;
use Nette\Security\User as NetteUser;

abstract class DefaultPresenterTemplate extends Template
{
    public NetteUser $user;

    public string $locale;

    public string $lang;

    public string $pageDescription = '';

    public bool $recaptchaEnabled;
}
