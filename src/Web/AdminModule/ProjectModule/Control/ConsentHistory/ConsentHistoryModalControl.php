<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentHistory;

use App\ReadModel\Consent\ConsentView;
use App\Web\Ui\Modal\AbstractModalControl;

final class ConsentHistoryModalControl extends AbstractModalControl
{
    private ConsentView $consentView;

    private ConsentHistoryControlFactoryInterface $consentHistoryControlFactory;

    public function __construct(ConsentView $consentView, ConsentHistoryControlFactoryInterface $consentHistoryControlFactory)
    {
        $this->consentView = $consentView;
        $this->consentHistoryControlFactory = $consentHistoryControlFactory;
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof ConsentHistoryModalTemplate);

        $template->consentView = $this->consentView;
    }

    protected function createComponentHistory(): ConsentHistoryControl
    {
        return $this->consentHistoryControlFactory->create($this->consentView->id, $this->consentView->projectId);
    }
}
