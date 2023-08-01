<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\SignIn;

use App\Web\FrontModule\Control\SignIn\Event\AuthenticationFailedEvent;
use App\Web\FrontModule\Control\SignIn\Event\LoggedInEvent;
use App\Web\Ui\Control;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use Nepada\FormRenderer\TemplateRenderer;
use Nette\Application\UI\Form;
use SixtyEightPublishers\UserBundle\Application\Exception\AuthenticationException;
use SixtyEightPublishers\UserBundle\Application\Exception\IdentityException;
use SixtyEightPublishers\UserBundle\Bridge\Nette\Security\Identity;

final class SignInControl extends Control
{
    use FormFactoryOptionsTrait;

    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    protected function createComponentForm(): Form
    {
        $form = $this->formFactory->create($this->getFormFactoryOptions());
        $renderer = $form->getRenderer();
        assert($renderer instanceof TemplateRenderer);

        $form->setTranslator($this->getPrefixedTranslator());
        $renderer->importTemplate(__DIR__ . '/templates/form.imports.latte');

        $form->addText('username')
            ->setRequired('username.required')
            ->setHtmlAttribute('placeholder', 'username.field')
            ->setHtmlAttribute('autocomplete', 'username');

        $form->addPassword('password')
            ->setRequired('password.required')
            ->setHtmlAttribute('placeholder', 'password.field')
            ->setHtmlAttribute('autocomplete', 'current-password');

        $form->addCheckbox('remember_me', 'remember_me.field');

        $form->addSubmit('login', 'login.field');

        $form->onSuccess[] = function (Form $form): void {
            $this->login($form);
        };

        return $form;
    }

    /**
     * @throws \Nette\Security\AuthenticationException|IdentityException
     */
    private function login(Form $form): void
    {
        try {
            $values = $form->values;

            $this->getUser()->setExpiration($values->remember_me ? '14 days' : '6 hours');
            $this->getUser()->login($values->username, $values->password);

            $identity = $this->getUser()->getIdentity();
            assert($identity instanceof Identity);

            $this->dispatchEvent(new LoggedInEvent($identity->data()));
        } catch (AuthenticationException $e) {
            $this->dispatchEvent(new AuthenticationFailedEvent($e));
        }
    }
}
