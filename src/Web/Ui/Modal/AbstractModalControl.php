<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal;

use App\Web\Ui\Control;

/**
 * @property-read AbstractModalTemplate $template
 */
abstract class AbstractModalControl extends Control
{
	/**
	 * @return string
	 */
	public function toString(): string
	{
		return $this->doRenderToString();
	}

	/**
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->modalName = $this->getUniqueId();
		$this->template->layout = __DIR__ . '/templates/abstractModalControl.latte';
	}
}
