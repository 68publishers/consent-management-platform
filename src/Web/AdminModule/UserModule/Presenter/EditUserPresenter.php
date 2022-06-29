<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Presenter;

use App\ReadModel\User\UserView;
use App\Application\Acl\UserResource;
use App\Web\Ui\Form\FormFactoryInterface;
use App\Web\AdminModule\Presenter\AdminPresenter;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;
use SixtyEightPublishers\FlashMessageBundle\Domain\FlashMessage;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\SmartNetteComponent\Annotation\IsAllowed;
use App\Web\AdminModule\UserModule\Control\UserForm\UserFormControl;
use SixtyEightPublishers\UserBundle\ReadModel\Query\GetUserByIdQuery;
use App\Web\AdminModule\UserModule\Control\UserForm\Event\UserUpdatedEvent;
use App\Web\AdminModule\UserModule\Control\UserForm\UserFormControlFactoryInterface;
use App\Web\AdminModule\UserModule\Control\UserForm\Event\UserFormProcessingFailedEvent;

/**
 * @IsAllowed(resource=UserResource::class, privilege=UserResource::UPDATE)
 */
final class EditUserPresenter extends AdminPresenter
{
	private UserFormControlFactoryInterface $userFormControlFactory;

	private QueryBusInterface $queryBus;

	private UserView $userView;

	/**
	 * @param \App\Web\AdminModule\UserModule\Control\UserForm\UserFormControlFactoryInterface $userFormControlFactory
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface                   $queryBus
	 */
	public function __construct(UserFormControlFactoryInterface $userFormControlFactory, QueryBusInterface $queryBus)
	{
		parent::__construct();

		$this->userFormControlFactory = $userFormControlFactory;
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
		$userView = UserId::isValid($id) ? $this->queryBus->dispatch(GetUserByIdQuery::create($id)) : NULL;

		if (!$userView instanceof UserView || NULL !== $userView->deletedAt) {
			$this->subscribeFlashMessage(FlashMessage::warning('user_not_found'));
			$this->redirect('Users:');
		}

		$this->userView = $userView;

		$this->addBreadcrumbItem($this->getPrefixedTranslator()->translate('page_title'));
		$this->addBreadcrumbItem($this->userView->username->value());
	}

	/**
	 * @return \App\Web\AdminModule\UserModule\Control\UserForm\UserFormControl
	 */
	protected function createComponentUserForm(): UserFormControl
	{
		$control = $this->userFormControlFactory->create($this->userView);

		$control->setFormFactoryOptions([
			FormFactoryInterface::OPTION_AJAX => TRUE,
		]);

		$control->addEventListener(UserUpdatedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::success('user_edited'));
		});

		$control->addEventListener(UserFormProcessingFailedEvent::class, function () {
			$this->subscribeFlashMessage(FlashMessage::error('user_edit_failed'));
		});

		return $control;
	}
}
