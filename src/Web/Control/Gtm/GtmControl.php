<?php

declare(strict_types=1);

namespace App\Web\Control\Gtm;

use App\Web\Ui\Control;

final class GtmControl extends Control
{
    private ?string $containerId;

    public function __construct(?string $containerId)
    {
        $this->containerId = $containerId;
    }

    public function renderScript(): void
    {
        $this->doRender('script');
    }

    public function renderNoscript(): void
    {
        $this->doRender('noscript');
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof GtmTemplate);

        $template->containerId = $this->containerId;
    }
}
