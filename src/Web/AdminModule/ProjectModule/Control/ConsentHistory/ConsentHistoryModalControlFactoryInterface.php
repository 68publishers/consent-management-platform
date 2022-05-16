<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentHistory;

use App\ReadModel\Consent\ConsentView;

interface ConsentHistoryModalControlFactoryInterface
{
	/**
	 * @param \App\ReadModel\Consent\ConsentView $consentView
	 *
	 * @return \App\Web\AdminModule\ProjectModule\Control\ConsentHistory\ConsentHistoryModalControl
	 */
	public function create(ConsentView $consentView): ConsentHistoryModalControl;
}
