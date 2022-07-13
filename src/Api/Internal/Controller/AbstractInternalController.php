<?php

declare(strict_types=1);

namespace App\Api\Internal\Controller;

use App\Api\Controller\AbstractController;
use Apitte\Core\Annotation\Controller as Api;

/**
 * @Api\Path("/internal")
 */
abstract class AbstractInternalController extends AbstractController
{
}
