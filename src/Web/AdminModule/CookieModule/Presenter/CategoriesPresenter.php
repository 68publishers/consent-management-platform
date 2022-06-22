<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\Web\AdminModule\Presenter\AdminPresenter;
use App\Web\AdminModule\CookieModule\Control\CategoryList\CategoryListControl;
use App\Web\AdminModule\CookieModule\Control\CategoryList\CategoryListControlFactoryInterface;

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
