<?php

declare(strict_types=1);

namespace App\Api\V1\Controller;

use Apitte\Core\Annotation\Controller as Api;
use App\Api\Controller\AbstractController;

/**
 * @Api\Path("/v1")
 */
abstract class AbstractV1Controller extends AbstractController
{
}
