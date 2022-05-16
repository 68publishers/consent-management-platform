<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentHistory;

use App\Domain\Consent\ValueObject\ConsentId;

interface ConsentHistoryControlFactoryInterface
{
	/**
	 * @param \App\Domain\Consent\ValueObject\ConsentId $consentId
	 *
	 * @return \App\Web\AdminModule\ProjectModule\Control\ConsentHistory\ConsentHistoryControl
	 */
	public function create(ConsentId $consentId): ConsentHistoryControl;
}
