<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm;

use App\Domain\Project\Command\UpdateProjectCommand;
use App\ReadModel\CookieProvider\CookieProviderSelectOptionView;
use App\ReadModel\CookieProvider\FindCookieProviderSelectOptionsQuery;
use App\ReadModel\Project\ProjectView;
use App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm\Event\OtherProvidersFormProcessingFailedEvent;
use App\Web\AdminModule\ProjectModule\Control\OtherProvidersForm\Event\OtherProvidersUpdatedEvent;
use App\Web\Ui\Control;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Ui\Form\FormFactoryOptionsTrait;
use Nette\Application\UI\Form;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use Throwable;

final class OtherProvidersFormControl extends Control
{
    use FormFactoryOptionsTrait;

    public function __construct(
        private readonly ProjectView $projectView,
        private readonly FormFactoryInterface $formFactory,
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
    ) {}

    protected function createComponentForm(): Form
    {
        $form = $this->formFactory->create($this->getFormFactoryOptions());
        $translator = $this->getPrefixedTranslator();

        $form->setTranslator($translator);

        $form->addMultiSelect('cookie_providers', 'cookie_providers.field')
            ->setItems($this->getCookieProviderOptions())
            ->checkDefaultValue(false)
            ->setTranslator(null)
            ->setOption('searchbar', true)
            ->setOption('tags', true);

        $form->addProtection('//layout.form_protection');

        $form->addSubmit('save', 'update.field');

        $form->setDefaults([
            'cookie_providers' => array_map(static fn (CookieProviderSelectOptionView $view): string => $view->id->toString(), $this->queryBus->dispatch(FindCookieProviderSelectOptionsQuery::assignedToProject($this->projectView->id->toString()))),
        ]);

        $form->onSuccess[] = function (Form $form): void {
            $this->saveProviders($form);
        };

        return $form;
    }

    private function saveProviders(Form $form): void
    {
        $values = $form->values;

        $command = UpdateProjectCommand::create($this->projectView->id->toString())
            ->withCookieProviderIds($values->cookie_providers);

        try {
            $this->commandBus->dispatch($command);
        } catch (Throwable $e) {
            $this->logger->error((string) $e);
            $this->dispatchEvent(new OtherProvidersFormProcessingFailedEvent($e));

            return;
        }

        $this->dispatchEvent(new OtherProvidersUpdatedEvent());
        $this->redrawControl();
    }

    private function getCookieProviderOptions(): array
    {
        $options = [];

        /** @var CookieProviderSelectOptionView $cookieProviderSelectOptionView */
        foreach ($this->queryBus->dispatch(FindCookieProviderSelectOptionsQuery::all()) as $cookieProviderSelectOptionView) {
            $options += $cookieProviderSelectOptionView->toOption();
        }

        return $options;
    }
}
