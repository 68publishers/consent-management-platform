<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail;

use App\ReadModel\ConsentSettings\ConsentSettingsView;
use App\Web\Ui\Modal\AbstractModalControl;

final class ConsentSettingsDetailModalControl extends AbstractModalControl
{
    public function __construct(
        private readonly ConsentSettingsView $consentSettingsView,
        private readonly ConsentSettingsDetailControlFactoryInterface $consentHistoryControlFactory,
    ) {}

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof ConsentSettingsDetailModalTemplate);

        $template->consentSettingsView = $this->consentSettingsView;
    }

    protected function createComponentConsentSettingsDetail(): ConsentSettingsDetailControl
    {
        return $this->consentHistoryControlFactory->create($this->consentSettingsView);
    }
}
