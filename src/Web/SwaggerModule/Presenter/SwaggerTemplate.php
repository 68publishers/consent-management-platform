<?php

declare(strict_types=1);

namespace App\Web\SwaggerModule\Presenter;

use App\Application\OpenApiConfiguration;
use Nette\Bridges\ApplicationLatte\Template;

final class SwaggerTemplate extends Template
{
	public OpenApiConfiguration $configuration;
}
