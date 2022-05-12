<?php

declare(strict_types=1);

namespace App\Domain\Consent\Exception;

use DomainException;
use App\Domain\Consent\ValueObject\ConsentId;
use App\Domain\Consent\ValueObject\UserIdentifier;

final class UserIdentifierExistsException extends DomainException
{
	private ConsentId $consentId;

	private UserIdentifier $userIdentifier;

	/**
	 * @param \App\Domain\Consent\ValueObject\ConsentId      $consentId
	 * @param \App\Domain\Consent\ValueObject\UserIdentifier $userIdentifier
	 * @param string                                         $message
	 */
	private function __construct(ConsentId $consentId, UserIdentifier $userIdentifier, string $message)
	{
		parent::__construct($message);

		$this->consentId = $consentId;
		$this->userIdentifier = $userIdentifier;
	}

	/**
	 * @param \App\Domain\Consent\ValueObject\ConsentId      $consentId
	 * @param \App\Domain\Consent\ValueObject\UserIdentifier $userIdentifier
	 *
	 * @return static
	 */
	public static function create(ConsentId $consentId, UserIdentifier $userIdentifier): self
	{
		return new self($consentId, $userIdentifier, sprintf(
			'Consent with user identifier %s exists.',
			$userIdentifier->value()
		));
	}

	/**
	 * @return \App\Domain\Consent\ValueObject\ConsentId
	 */
	public function consentId(): ConsentId
	{
		return $this->consentId;
	}

	/**
	 * @return \App\Domain\Consent\ValueObject\UserIdentifier
	 */
	public function userIdentifier(): UserIdentifier
	{
		return $this->userIdentifier;
	}
}
