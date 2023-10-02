<?php

declare(strict_types=1);

namespace App\Web\Ui\Form;

use Nette\Application\UI\Form;

interface FormFactoryInterface
{
    public const OPTION_AJAX = 'ajax';
    public const OPTION_IMPORTS = 'imports';
    public const OPTION_TEMPLATE_VARIABLES = 'template_variables';

    public function create(array $options = []): Form;
}
