<?php

declare(strict_types=1);

namespace App\Domain\Consent;

use App\Domain\Consent\ValueObject\ConsentId;

interface ConsentRepositoryInterface
{
	/**
	 * @param \App\Domain\Consent\Consent $consent
	 *
	 * @return void
	 */
	public function save(Consent $consent): void;

	/**
	 * @param \App\Domain\Consent\ValueObject\ConsentId $id
	 *
	 * @return \App\Domain\Consent\Consent
	 * @throws \App\Domain\Consent\Exception\ConsentNotFoundException
	 */
	public function get(ConsentId $id): Consent;
}
