<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CookieForm;

use App\Application\GlobalSettings\ValidLocalesProvider;
use App\ReadModel\Cookie\CookieView;
use App\Web\Ui\Modal\AbstractModalControl;

final class CookieFormModalControl extends AbstractModalControl
{
    public function __construct(
        private readonly ValidLocalesProvider $validLocalesProvider,
        private readonly ?CookieView $default,
        private readonly CookieFormControlFactoryInterface $cookieFormControlFactory,
    ) {}

    public function getInnerControl(): CookieFormControl
    {
        return $this->getComponent('cookieForm');
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof CookieFormModalTemplate);

        $template->default = $this->default;
    }

    protected function createComponentCookieForm(): CookieFormControl
    {
        return $this->cookieFormControlFactory->create($this->validLocalesProvider, $this->default);
    }
}
