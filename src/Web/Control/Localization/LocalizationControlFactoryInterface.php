<?php

declare(strict_types=1);

namespace App\Web\Control\Localization;

interface LocalizationControlFactoryInterface
{
	/**
	 * @return \App\Web\Control\Localization\LocalizationControl
	 */
	public function create(): LocalizationControl;
}
