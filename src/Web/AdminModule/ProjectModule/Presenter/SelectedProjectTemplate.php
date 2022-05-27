<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\ReadModel\Project\ProjectView;
use App\Application\GlobalSettings\Locale;
use App\Web\AdminModule\Presenter\AdminTemplate;

abstract class SelectedProjectTemplate extends AdminTemplate
{
	public ProjectView $projectView;

	/** @var \App\Application\GlobalSettings\Locale[] */
	public array $projectLocales;

	public ?Locale $defaultProjectLocale = NULL;
}
