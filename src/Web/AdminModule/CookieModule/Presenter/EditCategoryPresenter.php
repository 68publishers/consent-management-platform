<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\ReadModel\Category\CategoryView;
use App\Application\Acl\CategoryResource;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Domain\Category\ValueObject\CategoryId;
use App\ReadModel\Category\GetCategoryByIdQuery;
use App\Web\AdminModule\Presenter\AdminPresenter;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\SmartNetteComponent\Annotation\IsAllowed;
use App\Web\AdminModule\CookieModule\Control\CategoryForm\CategoryFormControl;
use App\Web\AdminModule\CookieModule\Control\CategoryForm\Event\CategoryUpdatedEvent;
use App\Web\AdminModule\CookieModule\Control\CategoryForm\CategoryFormControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\CategoryForm\Event\CategoryFormProcessingFailedEvent;

/**
 * @IsAllowed(resource=CategoryResource::class, privilege=CategoryResource::UPDATE)
 */
final class EditCategoryPresenter extends AdminPresenter
{
	private CategoryFormControlFactoryInterface $categoryFormControlFactory;

	private QueryBusInterface $queryBus;

	private CategoryView $categoryView;

	/**
	 * @param \App\Web\AdminModule\CookieModule\Control\CategoryForm\CategoryFormControlFactoryInterface $categoryFormControlFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface                             $queryBus
	 */
	public function __construct(CategoryFormControlFactoryInterface $categoryFormControlFactory, QueryBusInterface $queryBus)
	{
		parent::__construct();

		$this->categoryFormControlFactory = $categoryFormControlFactory;
		$this->queryBus = $queryBus;
	}

	/**
	 * @param string $id
	 *
	 * @return void
	 * @throws \Nette\Application\AbortException
	 */
	public function actionDefault(string $id): void
	{
		$categoryView = CategoryId::isValid($id) ? $this->queryBus->dispatch(GetCategoryByIdQuery::create($id)) : NULL;

		if (!$categoryView instanceof CategoryView || NULL !== $categoryView->deletedAt) {
			$this->subscribeFlashMessage(FlashMessage::warning('category_not_found'));
			$this->redirect('Categories:');
		}

		$this->categoryView = $categoryView;

		$this->setBreadcrumbItems([
			$this->getPrefixedTranslator()->translate('page_title'),
			$this->categoryView->code->value(),
		]);
	}

	/**
	 * @return \App\Web\AdminModule\CookieModule\Control\CategoryForm\CategoryFormControl
	 */
	protected function createComponentCategoryForm(): CategoryFormControl
	{
		$control = $this->categoryFormControlFactory->create($this->categoryView);

		$control->setFormFactoryOptions([
			FormFactoryInterface::OPTION_AJAX => TRUE,
		]);

		$control->addEventListener(CategoryUpdatedEvent::class, function (CategoryUpdatedEvent $event) {
			$this->subscribeFlashMessage(FlashMessage::success('category_updated'));

			$this->setBreadcrumbItems([
				$this->getPrefixedTranslator()->translate('page_title'),
				$event->newCode(),
			]);

			$this->redrawControl('heading');
		});

		$control->addEventListener(CategoryFormProcessingFailedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::error('category_update_failed'));
		});

		return $control;
	}
}
