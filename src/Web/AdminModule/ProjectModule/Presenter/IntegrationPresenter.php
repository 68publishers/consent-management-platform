<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\Application\Acl\ProjectIntegrationResource;
use App\Application\GlobalSettings\EnabledEnvironmentsResolver;
use App\Domain\GlobalSettings\ValueObject\Environment;
use App\Web\AdminModule\ProjectModule\Control\TemplatesForm\Event\TemplatesFormProcessingFailedEvent;
use App\Web\AdminModule\ProjectModule\Control\TemplatesForm\Event\TemplatesUpdatedEvent;
use App\Web\AdminModule\ProjectModule\Control\TemplatesForm\TemplatesFormControl;
use App\Web\AdminModule\ProjectModule\Control\TemplatesForm\TemplatesFormControlFactoryInterface;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\Utils\Color;
use Nette\InvalidStateException;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: ProjectIntegrationResource::class, privilege: ProjectIntegrationResource::READ)]
final class IntegrationPresenter extends SelectedProjectPresenter
{
    public function __construct(
        private readonly TemplatesFormControlFactoryInterface $templatesFormControlFactory,
    ) {
        parent::__construct();
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $environments = EnabledEnvironmentsResolver::resolveProjectEnvironments(
            globalSettingsEnvironments: $this->globalSettings->environments(),
            projectEnvironments: $this->projectView->environments,
        );

        $template = $this->getTemplate();
        assert($template instanceof IntegrationTemplate);

        $template->appHost = $this->getHttpRequest()->getUrl()->getHostUrl();
        $template->environments = array_merge(
            [
                [
                    'code' => '//default//',
                    'name' => $this->getTranslator()->translate('//layout.default_environment'),
                    'color' => '#e5e7eb',
                    'fontColor' => '#000000',
                ],
            ],
            array_values(
                array_map(
                    static fn (Environment $environment): array => [
                        'code' => $environment->code,
                        'name' => $environment->name,
                        'color' => $environment->color->value(),
                        'fontColor' => Color::resolveFontColor($environment->color->value()),
                    ],
                    $environments,
                ),
            ),
        );
    }

    protected function createComponentTemplatesForm(): TemplatesFormControl
    {
        if (!$this->getUser()->isAllowed(ProjectIntegrationResource::class, ProjectIntegrationResource::UPDATE)) {
            throw new InvalidStateException('The user is not allowed to update project\'s templates.');
        }

        $control = $this->templatesFormControlFactory->create($this->projectView, $this->validLocalesProvider->withLocalesConfig($this->projectView->locales));

        $control->setFormFactoryOptions([
            FormFactoryInterface::OPTION_AJAX => true,
        ]);

        $control->addEventListener(TemplatesUpdatedEvent::class, function () {
            $this->subscribeFlashMessage(FlashMessage::success('templates_updated'));
        });

        $control->addEventListener(TemplatesFormProcessingFailedEvent::class, function () {
            $this->subscribeFlashMessage(FlashMessage::error('templates_updated_failed'));
        });

        return $control;
    }
}
