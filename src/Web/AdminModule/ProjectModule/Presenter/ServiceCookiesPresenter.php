<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Presenter;

use App\Application\Acl\ProjectCookieResource;
use App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControl;
use App\Web\AdminModule\CookieModule\Control\CookieList\CookieListControlFactoryInterface;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: ProjectCookieResource::class, privilege: ProjectCookieResource::READ)]
final class ServiceCookiesPresenter extends SelectedProjectPresenter
{
    private CookieListControlFactoryInterface $cookieListControlFactory;

    public function __construct(CookieListControlFactoryInterface $cookieListControlFactory)
    {
        parent::__construct();

        $this->cookieListControlFactory = $cookieListControlFactory;
    }

    protected function startup(): void
    {
        parent::startup();

        $this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
    }

    protected function createComponentList(): CookieListControl
    {
        $control = $this->cookieListControlFactory->create(
            $this->validLocalesProvider->withLocalesConfig($this->projectView->locales),
        );

        $control->configureActions(false, false);
        $control->projectOnly($this->projectView->id, true);

        return $control;
    }
}
