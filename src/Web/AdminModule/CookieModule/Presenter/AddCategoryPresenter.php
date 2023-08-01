<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\Application\Acl\CategoryResource;
use App\Web\AdminModule\CookieModule\Control\CategoryForm\CategoryFormControl;
use App\Web\AdminModule\CookieModule\Control\CategoryForm\CategoryFormControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\CategoryForm\Event\CategoryCreatedEvent;
use App\Web\AdminModule\CookieModule\Control\CategoryForm\Event\CategoryFormProcessingFailedEvent;
use App\Web\AdminModule\Presenter\AdminPresenter;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: CategoryResource::class, privilege: CategoryResource::CREATE)]
final class AddCategoryPresenter extends AdminPresenter
{
    public function __construct(
        private readonly CategoryFormControlFactoryInterface $categoryFormControlFactory,
    ) {
        parent::__construct();
    }

    public function actionDefault(): void
    {
        $this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
    }

    protected function createComponentCategoryForm(): CategoryFormControl
    {
        $control = $this->categoryFormControlFactory->create();

        $control->addEventListener(CategoryCreatedEvent::class, function (CategoryCreatedEvent $event) {
            $this->subscribeFlashMessage(FlashMessage::success('category_created'));
            $this->redirect('EditCategory:', ['id' => $event->categoryId()->toString()]);
        });

        $control->addEventListener(CategoryFormProcessingFailedEvent::class, function () {
            $this->subscribeFlashMessage(FlashMessage::error('category_creation_failed'));
        });

        return $control;
    }
}
