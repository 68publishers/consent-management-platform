<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal\Confirm;

use Nette\Bridges\ApplicationLatte\Template;
use Nette\Utils\Html;

final class ConfirmModalTemplate extends Template
{
    public Html $title;

    public Html $question;

    public array $args = [];

    public ?string $yesButtonText = null;

    public ?string $noButtonText = null;
}
