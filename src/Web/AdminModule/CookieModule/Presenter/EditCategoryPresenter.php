<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Presenter;

use App\Application\Acl\CategoryResource;
use App\Domain\Category\ValueObject\CategoryId;
use App\ReadModel\Category\CategoryView;
use App\ReadModel\Category\GetCategoryByIdQuery;
use App\Web\AdminModule\CookieModule\Control\CategoryForm\CategoryFormControl;
use App\Web\AdminModule\CookieModule\Control\CategoryForm\CategoryFormControlFactoryInterface;
use App\Web\AdminModule\CookieModule\Control\CategoryForm\Event\CategoryFormProcessingFailedEvent;
use App\Web\AdminModule\CookieModule\Control\CategoryForm\Event\CategoryUpdatedEvent;
use App\Web\AdminModule\Presenter\AdminPresenter;
use App\Web\Ui\Form\FormFactoryInterface;
use Nette\Application\AbortException;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\SmartNetteComponent\Attribute\Allowed;

#[Allowed(resource: CategoryResource::class, privilege: CategoryResource::UPDATE)]
final class EditCategoryPresenter extends AdminPresenter
{
    private CategoryFormControlFactoryInterface $categoryFormControlFactory;

    private QueryBusInterface $queryBus;

    private CategoryView $categoryView;

    public function __construct(CategoryFormControlFactoryInterface $categoryFormControlFactory, QueryBusInterface $queryBus)
    {
        parent::__construct();

        $this->categoryFormControlFactory = $categoryFormControlFactory;
        $this->queryBus = $queryBus;
    }

    /**
     * @throws AbortException
     */
    public function actionDefault(string $id): void
    {
        $categoryView = CategoryId::isValid($id) ? $this->queryBus->dispatch(GetCategoryByIdQuery::create($id)) : null;

        if (!$categoryView instanceof CategoryView) {
            $this->subscribeFlashMessage(FlashMessage::warning('category_not_found'));
            $this->redirect('Categories:');
        }

        $this->categoryView = $categoryView;

        $this->setBreadcrumbItems([
            $this->getPrefixedTranslator()->translate('page_title'),
            $this->categoryView->code->value(),
        ]);
    }

    protected function createComponentCategoryForm(): CategoryFormControl
    {
        $control = $this->categoryFormControlFactory->create($this->categoryView);

        $control->setFormFactoryOptions([
            FormFactoryInterface::OPTION_AJAX => true,
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
