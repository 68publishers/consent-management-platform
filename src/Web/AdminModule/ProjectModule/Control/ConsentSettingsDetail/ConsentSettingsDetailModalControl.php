<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentSettingsDetail;

use App\ReadModel\ConsentSettings\ConsentSettingsView;
use App\Web\Ui\Modal\AbstractModalControl;

final class ConsentSettingsDetailModalControl extends AbstractModalControl
{
    private ConsentSettingsView $consentSettingsView;

    private ConsentSettingsDetailControlFactoryInterface $consentHistoryControlFactory;

    public function __construct(ConsentSettingsView $consentSettingsView, ConsentSettingsDetailControlFactoryInterface $consentHistoryControlFactory)
    {
        $this->consentSettingsView = $consentSettingsView;
        $this->consentHistoryControlFactory = $consentHistoryControlFactory;
    }

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
