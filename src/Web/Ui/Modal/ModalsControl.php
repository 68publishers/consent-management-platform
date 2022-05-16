<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal;

use App\Web\Ui\Control;
use App\Web\Ui\Modal\Dispatcher\ModalDispatcherInterface;

/**
 * @property-read ModalsTemplate $template
 */
final class ModalsControl extends Control
{
	private ModalDispatcherInterface $dispatcher;

	private HtmlId $elementId;

	/**
	 * @param \App\Web\Ui\Modal\Dispatcher\ModalDispatcherInterface $dispatcher
	 * @param \App\Web\Ui\Modal\HtmlId                              $elementId
	 */
	public function __construct(ModalDispatcherInterface $dispatcher, HtmlId $elementId)
	{
		$this->dispatcher = $dispatcher;
		$this->elementId = $elementId;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \JsonException
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->elementId = $this->elementId;
		$this->template->payload = json_encode($this->dispatcher, JSON_THROW_ON_ERROR);
	}
}
