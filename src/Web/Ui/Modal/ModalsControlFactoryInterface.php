<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal;

interface ModalsControlFactoryInterface
{
    public function create(HtmlId $elementId): ModalsControl;
}
