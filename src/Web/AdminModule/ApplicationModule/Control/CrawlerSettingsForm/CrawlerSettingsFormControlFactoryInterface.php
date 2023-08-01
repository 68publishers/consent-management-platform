<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ApplicationModule\Control\CrawlerSettingsForm;

interface CrawlerSettingsFormControlFactoryInterface
{
    public function create(): CrawlerSettingsFormControl;
}
