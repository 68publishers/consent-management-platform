<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\ExternalAuthList;

use App\ReadModel\User\ExternalAuthView;
use Nette\Bridges\ApplicationLatte\Template;

final class ExternalAuthListTemplate extends Template
{
    /** @var array<int, ExternalAuthView> */
    public array $externalAuths;
}
