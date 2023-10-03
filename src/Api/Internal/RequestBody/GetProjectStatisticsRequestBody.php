<?php

declare(strict_types=1);

namespace App\Api\Internal\RequestBody;

use Apitte\Core\Mapping\Request\BasicEntity;
use Symfony\Component\Validator\Constraints as Assert;

final class GetProjectStatisticsRequestBody extends BasicEntity
{
    /**
     * @Assert\NotBlank()
     * @Assert\Uuid(),
     */
    public ?string $userId = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Timezone(),
     */
    public ?string $timezone = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string"),
     */
    public ?string $locale = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string"),
     */
    public string $startDate = '';

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string"),
     */
    public string $endDate = '';

    /**
     * @Assert\NotBlank()
     * @Assert\AtLeastOneOf({
     *     @Assert\Type("string"),
     *     @Assert\All({
     *         @Assert\Type("string")
     *     })
     * })
     *
     * @var string|array<string>|NULL
     */
    public string|array|null $projects = null;

    public ?string $environment = null;
}
