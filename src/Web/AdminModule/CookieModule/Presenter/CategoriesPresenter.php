<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\Application\Acl\CategoryResource;
use App\Web\AdminModule\CookieModule\Control\CategoryList\CategoryListControl;
use App\Web\AdminModule\CookieModule\Control\CategoryList\CategoryListControlFactoryInterface;
use App\Web\AdminModule\Presenter\AdminPresenter;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: CategoryResource::class, privilege: CategoryResource::READ)]
final class CategoriesPresenter extends AdminPresenter
{
    public function __construct(
        private readonly CategoryListControlFactoryInterface $categoryListControlFactory,
    ) {
        parent::__construct();
    }

    protected function startup(): void
    {
        parent::startup();

        $this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
    }

    protected function createComponentList(): CategoryListControl
    {
        return $this->categoryListControlFactory->create($this->validLocalesProvider->getValidDefaultLocale());
    }
}
