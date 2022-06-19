<?php

declare(strict_types=1);

namespace App\Api\V1\RequestBody;

use Apitte\Core\Mapping\Request\BasicEntity;

final class GetCookieProvidersRequestBody extends BasicEntity
{
	public ?string $locale = NULL;
}
