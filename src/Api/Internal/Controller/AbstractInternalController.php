<?php

declare(strict_types=1);

namespace App\Api\Internal\Controller;

use Apitte\Core\Annotation\Controller as Api;
use App\Api\Controller\AbstractController;

/**
 * @Api\Path("/internal")
 */
abstract class AbstractInternalController extends AbstractController
{
}
