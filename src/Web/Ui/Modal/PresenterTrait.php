<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal;

use App\Web\Ui\Modal\Dispatcher\Event\ModalClosedEvent;
use App\Web\Ui\Modal\Dispatcher\ModalDispatcherInterface;
use App\Web\Ui\Modal\Dispatcher\Event\ModalDispatchedEvent;

/**
 * @method bool isAjax()
 */
trait PresenterTrait
{
	use ControlTrait {
		injectModalDispatcher as _injectModalDispatcher;
	}

	private ModalsControlFactoryInterface $modalsControlFactory;

	/**
	 * @param \App\Web\Ui\Modal\Dispatcher\ModalDispatcherInterface $modalDispatcher
	 *
	 * @return void
	 */
	public function injectModalDispatcher(ModalDispatcherInterface $modalDispatcher): void
	{
		$this->_injectModalDispatcher($modalDispatcher);

		$eventDispatcher = $modalDispatcher->getEventDispatcher();

		$redraw = function () {
			if ($this->isAjax()) {
				$modals = $this['modals'];
				assert($modals instanceof ModalsControl);

				$modals->redrawControl();
			}
		};

		$eventDispatcher->addListener(ModalDispatchedEvent::class, $redraw);
		$eventDispatcher->addListener(ModalClosedEvent::class, $redraw);
	}

	/**
	 * @param \App\Web\Ui\Modal\ModalsControlFactoryInterface $modalsControlFactory
	 *
	 * @return void
	 */
	public function injectModalsControlFactory(ModalsControlFactoryInterface $modalsControlFactory): void
	{
		$this->modalsControlFactory = $modalsControlFactory;
	}

	/**
	 * @return \App\Web\Ui\Modal\ModalsControl
	 */
	protected function createComponentModals(): ModalsControl
	{
		return $this->modalsControlFactory->create(HtmlId::create('modals-payload'));
	}
}
