<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail;

use App\ReadModel\ConsentSettings\ConsentSettingsView;
use App\Web\Ui\Control;

final class ConsentSettingsDetailControl extends Control
{
    private ConsentSettingsView $consentSettingsView;

    public function __construct(ConsentSettingsView $consentSettingsView)
    {
        $this->consentSettingsView = $consentSettingsView;
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof ConsentSettingsDetailTemplate);

        $template->consentSettingsView = $this->consentSettingsView;
    }
}
