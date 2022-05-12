<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings\Exception;

use DomainException;
use App\Domain\Shared\ValueObject\Checksum;
use App\Domain\ConsentSettings\ValueObject\ConsentSettingsId;

final class ChecksumExistsException extends DomainException
{
	private ConsentSettingsId $consentSettingsId;

	private Checksum $checksum;

	/**
	 * @param \App\Domain\ConsentSettings\ValueObject\ConsentSettingsId $consentSettingsId
	 * @param \App\Domain\Shared\ValueObject\Checksum                   $checksum
	 * @param string                                                    $message
	 */
	private function __construct(ConsentSettingsId $consentSettingsId, Checksum $checksum, string $message)
	{
		parent::__construct($message);

		$this->consentSettingsId = $consentSettingsId;
		$this->checksum = $checksum;
	}

	/**
	 * @param \App\Domain\ConsentSettings\ValueObject\ConsentSettingsId $consentSettingsId
	 * @param \App\Domain\Shared\ValueObject\Checksum                   $checksum
	 *
	 * @return static
	 */
	public static function create(ConsentSettingsId $consentSettingsId, Checksum $checksum): self
	{
		return new self($consentSettingsId, $checksum, sprintf(
			'Consent settings with checksum %s exists.',
			$checksum->value()
		));
	}

	/**
	 * @return \App\Domain\ConsentSettings\ValueObject\ConsentSettingsId
	 */
	public function consentSettingsId(): ConsentSettingsId
	{
		return $this->consentSettingsId;
	}

	/**
	 * @return \App\Domain\Shared\ValueObject\Checksum
	 */
	public function checksum(): Checksum
	{
		return $this->checksum;
	}
}
