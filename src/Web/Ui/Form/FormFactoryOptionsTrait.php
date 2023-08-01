<?php

declare(strict_types=1);

namespace App\Web\Ui\Form;

trait FormFactoryOptionsTrait
{
    private array $formFactoryOptions = [];

    public function setFormFactoryOptions(array $options): void
    {
        $this->formFactoryOptions = $options;
    }

    protected function getFormFactoryOptions(): array
    {
        return $this->formFactoryOptions;
    }
}
