<?php

declare(strict_types=1);

namespace App\Api\Controller;

use Apitte\Core\UI\Controller\IController;
use Apitte\Core\Annotation\Controller as API;

/**
 * @API\Path("/api")
 */
abstract class AbstractController implements IController
{
}
