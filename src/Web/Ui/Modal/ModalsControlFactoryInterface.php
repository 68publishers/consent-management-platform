<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal;

interface ModalsControlFactoryInterface
{
	/**
	 * @param \App\Web\Ui\Modal\HtmlId $elementId
	 *
	 * @return \App\Web\Ui\Modal\ModalsControl
	 */
	public function create(HtmlId $elementId): ModalsControl;
}
