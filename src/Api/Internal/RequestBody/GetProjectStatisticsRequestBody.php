<?php

declare(strict_types=1);

namespace App\Api\Internal\RequestBody;

use Apitte\Core\Mapping\Request\BasicEntity;
use Symfony\Component\Validator\Constraints as Assert;

final class GetProjectStatisticsRequestBody extends BasicEntity
{
	/**
	 * @Assert\Type("string"),
	 *
	 * @var string|NULL
	 */
	public $locale;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Type("string"),
	 *
	 * @var string
	 */
	public $startDate;

	/**
	 * @Assert\NotBlank()
	 * @Assert\Type("string"),
	 *
	 * @var string
	 */
	public $endDate;

	/**
	 * @Assert\NotBlank()
	 * @Assert\AtLeastOneOf({
	 *     @Assert\Type("string"),
	 *     @Assert\All({
	 *         @Assert\Type("string")
	 *     })
	 * })
	 *
	 * @var string|string[]|NULL
	 */
	public $projects;
}
