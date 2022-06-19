<?php

declare(strict_types=1);

namespace App\Api\V1\RequestBody;

use Apitte\Core\Mapping\Request\BasicEntity;
use Symfony\Component\Validator\Constraints as Assert;

final class GetCookiesRequestBody extends BasicEntity
{
	public ?string $locale = NULL;

	/**
	 * @Assert\AtLeastOneOf({
	 *     @Assert\Type("string"),
	 *     @Assert\All({
	 *         @Assert\Type("string")
	 *     })
	 * })
	 *
	 * @var string|string[]|NULL
	 */
	public $category;
}
