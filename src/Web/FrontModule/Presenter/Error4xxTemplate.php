<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Presenter;

use App\Web\Ui\DefaultPresenterTemplate;

final class Error4xxTemplate extends DefaultPresenterTemplate
{
    public int $errorCode;

    public string $errorCodeString;
}
