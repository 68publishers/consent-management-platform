<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal;

use Nette\Application\UI\Presenter;
use App\Web\Ui\Modal\Dispatcher\ModalDispatcherInterface;

/**
 * @method string getParameterId(string $name)
 */
trait ControlTrait
{
	private ModalDispatcherInterface $modalDispatcher;
	
	/**
	 * @param \App\Web\Ui\Modal\Dispatcher\ModalDispatcherInterface $modalDispatcher
	 *
	 * @return void
	 */
	public function injectModalDispatcher(ModalDispatcherInterface $modalDispatcher): void
	{
		$this->modalDispatcher = $modalDispatcher;
	}

	/**
	 * @param string $modal
	 *
	 * @return void
	 */
	public function handleOpenModal(string $modal): void
	{
		$modalControl = $this[$modal];

		$this->openModal($modalControl, [
			ModalDispatcherInterface::PARAMS_ON_OPEN => [
				Presenter::SIGNAL_KEY => $this->getParameterId('openModal'),
				$this->getParameterId('modal') => $modal,
			],
			ModalDispatcherInterface::REMOVE_PARAMS_ON_CLOSE => [
				Presenter::SIGNAL_KEY,
				$this->getParameterId('modal') . '*',
			],
		], TRUE);
	}

	/**
	 * @param \App\Web\Ui\Modal\AbstractModalControl $control
	 * @param array                                  $metadata
	 * @param bool                                   $closePrevious
	 *
	 * @return void
	 */
	protected function openModal(AbstractModalControl $control, array $metadata = [], bool $closePrevious = FALSE): void
	{
		if ($closePrevious) {
			$this->closeModal();
		}

		$this->modalDispatcher->dispatch($control, $metadata);
	}

	/**
	 * @param string|NULL $name
	 *
	 * @return void
	 */
	protected function closeModal(?string $name = NULL): void
	{
		$this->modalDispatcher->close(NULL !== $name ? [$name] : []);
	}
}
