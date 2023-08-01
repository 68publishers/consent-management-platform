<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\Application\Acl\CategoryResource;
use App\Web\AdminModule\Presenter\AdminPresenter;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;
use App\Web\AdminModule\CookieModule\Control\CategoryList\CategoryListControl;
use App\Web\AdminModule\CookieModule\Control\CategoryList\CategoryListControlFactoryInterface;

#[Allowed(resource: CategoryResource::class, privilege: CategoryResource::READ)]
final class CategoriesPresenter extends AdminPresenter
{
	private CategoryListControlFactoryInterface $categoryListControlFactory;

	/**
	 * @param \App\Web\AdminModule\CookieModule\Control\CategoryList\CategoryListControlFactoryInterface $categoryListControlFactory
	 */
	public function __construct(CategoryListControlFactoryInterface $categoryListControlFactory)
	{
		parent::__construct();

		$this->categoryListControlFactory = $categoryListControlFactory;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function startup(): void
	{
		parent::startup();

		$this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
	}

	/**
	 * @return \App\Web\AdminModule\CookieModule\Control\CategoryList\CategoryListControl
	 */
	protected function createComponentList(): CategoryListControl
	{
		return $this->categoryListControlFactory->create($this->validLocalesProvider->getValidDefaultLocale());
	}
}
