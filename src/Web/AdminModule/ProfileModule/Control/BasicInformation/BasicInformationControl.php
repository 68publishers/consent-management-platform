<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProfileModule\Control\BasicInformation;

use App\Application\Localization\ApplicationDateTimeZone;
use App\Application\Localization\Profiles;
use App\ReadModel\User\UserView;
use App\Web\AdminModule\ProfileModule\Control\BasicInformation\Event\BasicInformationUpdatedEvent;
use App\Web\AdminModule\ProfileModule\Control\BasicInformation\Event\BasicInformationUpdateFailedEvent;
use App\Web\Ui\Control;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use Nepada\FormRenderer\TemplateRenderer;
use Nette\Application\UI\Form;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\UserBundle\Domain\Command\UpdateUserCommand;
use Throwable;

final class BasicInformationControl extends Control
{
    use FormFactoryOptionsTrait;

    private FormFactoryInterface $formFactory;

    private CommandBusInterface $commandBus;

    private Profiles $profiles;

    private UserView $userView;

    public function __construct(FormFactoryInterface $formFactory, CommandBusInterface $commandBus, Profiles $profiles, UserView $userView)
    {
        $this->formFactory = $formFactory;
        $this->commandBus = $commandBus;
        $this->profiles = $profiles;
        $this->userView = $userView;
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof BasicInformationTemplate);

        $template->userView = $this->userView;
    }

    protected function createComponentForm(): Form
    {
        $form = $this->formFactory->create($this->getFormFactoryOptions());
        $renderer = $form->getRenderer();
        assert($renderer instanceof TemplateRenderer);

        $form->setTranslator($this->getPrefixedTranslator());
        $renderer->importTemplate(__DIR__ . '/templates/form.imports.latte');
        $renderer->getTemplate()->profiles = $this->profiles;

        $form->addText('firstname', 'firstname.field')
            ->setRequired('firstname.required');

        $form->addText('surname', 'surname.field')
            ->setRequired('surname.required');

        $profiles = [];
        foreach ($this->profiles->all() as $profile) {
            $profiles[$profile->locale()] = $profile->name();
        }

        $form->addSelect('profile', 'profile.field', $profiles)
            ->setRequired('profile.required')
            ->setTranslator(null);

        $form->addSelect('timezone', 'timezone.field')
            ->setItems(ApplicationDateTimeZone::all(), false)
            ->setRequired('timezone.required')
            ->setTranslator(null)
            ->setOption('searchbar', true);

        $form->addProtection('//layout.form_protection');

        $form->addSubmit('save', 'save.field');

        $form->setDefaults([
            'firstname' => $this->userView->name->firstname(),
            'surname' => $this->userView->name->surname(),
            'profile' => $this->userView->profileLocale->value(),
            'timezone' => $this->userView->timezone->getName(),
        ]);

        $form->onSuccess[] = function (Form $form): void {
            $this->saveBasicInformation($form);
        };

        return $form;
    }

    private function saveBasicInformation(Form $form): void
    {
        $values = $form->values;
        $command = UpdateUserCommand::create($this->userView->id->toString())
            ->withFirstname($values->firstname)
            ->withSurname($values->surname)
            ->withParam('profile', $values->profile)
            ->withParam('timezone', $values->timezone);

        try {
            $this->commandBus->dispatch($command);
        } catch (Throwable $e) {
            $this->logger->error((string) $e);
            $this->dispatchEvent(new BasicInformationUpdateFailedEvent($e));

            return;
        }

        $this->dispatchEvent(new BasicInformationUpdatedEvent($this->userView->id, $this->userView->profileLocale->value(), $values->profile));
        $this->redrawControl();
    }
}
