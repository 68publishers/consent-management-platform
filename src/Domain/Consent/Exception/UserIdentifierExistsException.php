<?php

declare(strict_types=1);

namespace App\Domain\Consent\Exception;

use App\Domain\Consent\ValueObject\ConsentId;
use App\Domain\Consent\ValueObject\UserIdentifier;
use DomainException;

final class UserIdentifierExistsException extends DomainException
{
    private function __construct(
        private readonly ConsentId $consentId,
        private readonly UserIdentifier $userIdentifier,
        string $message,
    ) {
        parent::__construct($message);
    }

    public static function create(ConsentId $consentId, UserIdentifier $userIdentifier): self
    {
        return new self($consentId, $userIdentifier, sprintf(
            'Consent with user identifier %s exists.',
            $userIdentifier->value(),
        ));
    }

    public function consentId(): ConsentId
    {
        return $this->consentId;
    }

    public function userIdentifier(): UserIdentifier
    {
        return $this->userIdentifier;
    }
}
