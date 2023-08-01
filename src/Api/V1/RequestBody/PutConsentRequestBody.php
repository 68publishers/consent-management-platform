<?php

declare(strict_types=1);

namespace App\Api\V1\RequestBody;

use Apitte\Core\Mapping\Request\BasicEntity;
use Symfony\Component\Validator\Constraints as Assert;

final class PutConsentRequestBody extends BasicEntity
{
    public ?string $settingsChecksum = null;

    /**
     * @Assert\NotBlank
     * @Assert\All({
     *      @Assert\Type("boolean")
     * })
     */
    public array $consents = [];

    public array $attributes = [];
}
