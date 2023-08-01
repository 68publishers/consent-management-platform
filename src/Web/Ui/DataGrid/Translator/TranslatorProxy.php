<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\Translator;

use Nette\Localization\Translator;
use Nette\Utils\Strings;

final class TranslatorProxy implements Translator
{
    public function __construct(
        private readonly Translator $translator,
    ) {}

    public function translate($message, ...$parameters): string
    {
        if (is_string($message) && Strings::startsWith($message, 'ublaboo_datagrid')) {
            $message = '//' . $message;
        }

        return $this->translator->translate($message, ...$parameters);
    }
}
