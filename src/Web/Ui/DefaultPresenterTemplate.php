<?php

declare(strict_types=1);

namespace App\Web\Ui;

use Nette\Security\User as NetteUser;
use Nette\Bridges\ApplicationLatte\Template;

abstract class DefaultPresenterTemplate extends Template
{
	public NetteUser $user;

	public string $locale;

	public string $lang;

	public string $pageDescription = '';

	public bool $recaptchaEnabled;
}
