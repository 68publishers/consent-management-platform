<?php

declare(strict_types=1);

namespace App\Api\V1\RequestBody;

use Apitte\Core\Mapping\Request\BasicEntity;
use Symfony\Component\Validator\Constraints as Assert;

final class CookiesRequestBody extends BasicEntity
{
    public ?string $locale = null;

    /**
     * @Assert\AtLeastOneOf({
     *     @Assert\Type("string"),
     *     @Assert\All({
     *         @Assert\Type("string")
     *     })
     * })
     *
     * @var string|array<string>|NULL
     */
    public string|array|null $category = null;
}
