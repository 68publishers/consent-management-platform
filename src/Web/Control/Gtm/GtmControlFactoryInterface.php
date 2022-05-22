<?php

declare(strict_types=1);

namespace App\Web\Control\Gtm;

interface GtmControlFactoryInterface
{
	/**
	 * @return \App\Web\Control\Gtm\GtmControl
	 */
	public function create(): GtmControl;
}
