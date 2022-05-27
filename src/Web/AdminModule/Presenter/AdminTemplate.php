<?php

declare(strict_types=1);

namespace App\Web\AdminModule\Presenter;

use App\Web\Ui\DefaultPresenterTemplate;
use App\Application\GlobalSettings\Locale;
use SixtyEightPublishers\UserBundle\ReadModel\View\IdentityView;
use SixtyEightPublishers\TracyGitVersion\Repository\GitRepositoryInterface;

abstract class AdminTemplate extends DefaultPresenterTemplate
{
	public IdentityView $identity;

	public GitRepositoryInterface $gitRepository;

	/** @var \App\Application\GlobalSettings\Locale[] */
	public array $locales;

	public ?Locale $defaultLocale = NULL;
}
