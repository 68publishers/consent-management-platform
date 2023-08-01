<?php

declare(strict_types=1);

namespace App\Web\Ui\Form;

use Nette\Application\UI\Form;

interface FormFactoryInterface
{
    public const OPTION_AJAX = 'ajax';

    public function create(array $options = []): Form;
}
