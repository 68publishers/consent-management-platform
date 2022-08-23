<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ProjectModule\Control\ConsentHistory;

use App\Domain\Consent\ValueObject\ConsentId;
use App\Domain\Project\ValueObject\ProjectId;

interface ConsentHistoryControlFactoryInterface
{
	/**
	 * @param \App\Domain\Consent\ValueObject\ConsentId $consentId
	 * @param \App\Domain\Project\ValueObject\ProjectId $projectId
	 *
	 * @return \App\Web\AdminModule\ProjectModule\Control\ConsentHistory\ConsentHistoryControl
	 */
	public function create(ConsentId $consentId, ProjectId $projectId): ConsentHistoryControl;
}
