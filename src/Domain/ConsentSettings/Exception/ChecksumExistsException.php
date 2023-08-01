<?php

declare(strict_types=1);

namespace App\Domain\ConsentSettings\Exception;

use App\Domain\ConsentSettings\ValueObject\ConsentSettingsId;
use App\Domain\Shared\ValueObject\Checksum;
use DomainException;

final class ChecksumExistsException extends DomainException
{
    private ConsentSettingsId $consentSettingsId;

    private Checksum $checksum;

    private function __construct(ConsentSettingsId $consentSettingsId, Checksum $checksum, string $message)
    {
        parent::__construct($message);

        $this->consentSettingsId = $consentSettingsId;
        $this->checksum = $checksum;
    }

    public static function create(ConsentSettingsId $consentSettingsId, Checksum $checksum): self
    {
        return new self($consentSettingsId, $checksum, sprintf(
            'Consent settings with checksum %s exists.',
            $checksum->value(),
        ));
    }

    public function consentSettingsId(): ConsentSettingsId
    {
        return $this->consentSettingsId;
    }

    public function checksum(): Checksum
    {
        return $this->checksum;
    }
}
