<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal\Confirm;

use Nette\Utils\Html;

interface ConfirmModalControlFactoryInterface
{
    public function create(Html|string $title, Html|string $question, callable $callback, array $args = []): ConfirmModalControl;
}
