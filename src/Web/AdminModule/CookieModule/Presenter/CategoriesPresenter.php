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
    private CategoryListControlFactoryInterface $categoryListControlFactory;

    public function __construct(CategoryListControlFactoryInterface $categoryListControlFactory)
    {
        parent::__construct();

        $this->categoryListControlFactory = $categoryListControlFactory;
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
