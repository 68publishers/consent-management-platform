<?php

declare(strict_types=1);

namespace App\Api\Controller;

use Apitte\Core\Annotation\Controller as Api;
use Apitte\Core\UI\Controller\IController;

/**
 * @Api\Path("/api")
 */
abstract class AbstractController implements IController
{
}
