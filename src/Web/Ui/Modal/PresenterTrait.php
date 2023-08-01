<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal;

use App\Web\Ui\Modal\Dispatcher\Event\ModalClosedEvent;
use App\Web\Ui\Modal\Dispatcher\Event\ModalDispatchedEvent;
use App\Web\Ui\Modal\Dispatcher\ModalDispatcherInterface;

/**
 * @method bool isAjax()
 */
trait PresenterTrait
{
    use ControlTrait {
        injectModalDispatcher as _injectModalDispatcher;
    }

    private ModalsControlFactoryInterface $modalsControlFactory;

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

    public function injectModalsControlFactory(ModalsControlFactoryInterface $modalsControlFactory): void
    {
        $this->modalsControlFactory = $modalsControlFactory;
    }

    protected function createComponentModals(): ModalsControl
    {
        return $this->modalsControlFactory->create(HtmlId::create('modals-payload'));
    }
}
