<?php

declare(strict_types=1);

namespace App\Api\V1\Controller;

use App\Api\Controller\AbstractController;
use Apitte\Core\Annotation\Controller as API;

/**
 * @API\Path("/v1")
 */
abstract class AbstractV1Controller extends AbstractController
{
}
