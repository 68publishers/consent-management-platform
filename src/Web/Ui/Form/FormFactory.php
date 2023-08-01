<?php

declare(strict_types=1);

namespace App\Web\Ui\Form;

use Nepada\FormRenderer\TemplateRendererFactory;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

final class FormFactory implements FormFactoryInterface
{
    public function __construct(
        private readonly TemplateRendererFactory $templateRendererFactory,
    ) {}

    public function create(array $options = []): Form
    {
        $form = new Form();

        if (true === ($options[self::OPTION_AJAX] ?? false)) {
            $form->elementPrototype->class('ajax', true);

            $form->onError[] = static function () use ($form) {
                $parent = $form->getParent();

                if ($parent instanceof Control) {
                    $parent->redrawControl();
                }
            };
        }

        $form->setRenderer($this->templateRendererFactory->create());

        return $form;
    }
}
