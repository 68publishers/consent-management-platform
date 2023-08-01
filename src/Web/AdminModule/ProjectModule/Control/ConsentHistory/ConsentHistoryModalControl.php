<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentHistory;

use App\ReadModel\Consent\ConsentView;
use App\Web\Ui\Modal\AbstractModalControl;

final class ConsentHistoryModalControl extends AbstractModalControl
{
    public function __construct(
        private readonly ConsentView $consentView,
        private readonly ConsentHistoryControlFactoryInterface $consentHistoryControlFactory,
    ) {}

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
